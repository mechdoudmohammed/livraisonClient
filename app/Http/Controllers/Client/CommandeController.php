<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Imports\CommandesImport;
use App\Models\Agence;
use App\Models\Article;
use App\Models\Commande;
use App\Models\DetailsCommandes;
use App\Models\HistoriqueCommande;
use App\Models\Notification;
use App\Models\Package;
use App\Models\Store;
use App\Models\Ville;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class CommandeController extends Controller
{
    // -------------------------------------------------------------------------
    // Helpers privés
    // -------------------------------------------------------------------------

    /**
     * Retourne l'utilisateur authentifié via Sanctum.
     */
    private function authUser()
    {
        return auth('sanctum')->user();
    }

    /**
     * Vérifie que l'utilisateur est Client ou EmployeClient et actif.
     */
    private function isAuthorized($user, bool $clientOnly = false): bool
    {
        if ($clientOnly) {
            return $user->role === 'Client' && $user->statut === 'Active';
        }

        return in_array($user->role, ['Client', 'EmployeClient']) && $user->statut === 'Active';
    }

    /**
     * Résout l'id_client et l'id_employe_client selon le rôle.
     */
    private function resolveClientIds($user): array
    {
        if ($user->role === 'EmployeClient') {
            return ['id_client' => $user->superviseur, 'id_employe_client' => $user->id];
        }

        return ['id_client' => $user->id, 'id_employe_client' => null];
    }

    /**
     * Clause where pour filtrer les commandes appartenant au client ou à son superviseur.
     */
    private function scopeClientOrSupervisor($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('commandes.id_client', $user->id)
              ->orWhere('commandes.id_client', $user->superviseur);
        });
    }

    /**
     * Calcule le prix de livraison (même ville ou standard).
     */
    private function resolvePrixLivraison(Ville $ville, $user): float
    {
        $agence = Agence::where('id_ville', $user->id_ville)->first();

        if ($agence && $user->id_ville == $agence->id_ville && $ville->id == $agence->id_ville) {
            return $ville->prix_livraison_meme_ville;
        }

        return $ville->prix_livraison;
    }

    /**
     * Génère un identifiant unique de commande.
     */
    private function generateIdCommande(Ville $ville, $user): string
    {
        return $ville->pref_ville
            . Carbon::now()->format('d')
            . Carbon::now()->format('m')
            . Carbon::now()->format('y')
            . $user->id
            . chr(rand(65, 90))
            . strtoupper(Str::random(3));
    }

    /**
     * Résout le store et retourne son id, ou null s'il n'est pas fourni.
     * Retourne false si le store ne appartient pas au client.
     */
    private function resolveStoreId(Request $request, $user)
    {
        if (!isset($request->store)) {
            return null;
        }

        $storeId = is_string($request->store) ? $request->store : $request->store['id'];

        $store = Store::where('id', $storeId)
            ->where(function ($q) use ($user) {
                $q->where('id_client', $user->id)
                  ->orWhere('id_client', $user->superviseur);
            })->first();

        return $store ? $storeId : false;
    }

    /**
     * Résout le type d'autorisation.
     */
    private function resolveTypeAutorisation(Request $request): string
    {
        if (isset($request->type_autorisation) && $request->type_autorisation) {
            return 'deny';
        }

        return 'allow';
    }

    /**
     * Construit le sous-query pour l'historique des commandes (commentaires spéciaux).
     */
    private function buildHistoriqueSubQuery()
    {
        $sub = HistoriqueCommande::orderBy('updated_at', 'desc');

        return DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
            ->whereIn('historiquecommandes.commentaire_commande', ['Pas de réponse', 'Retours envoye vers agence'])
            ->groupBy('id_commande');
    }

    /**
     * Valide les règles de validation standard d'une commande.
     */
    private function validateCommandeRequest(Request $request): void
    {
        $this->validate($request, [
            'ville_client_commande'      => 'required',
            'nom_client_commande'        => 'required|string',
            'adresse_client_commande'    => 'required|string',
            'telephone_client_commande'  => 'required|regex:/(0)[0-9]{9}$/',
            'prix_commande'              => 'required|numeric',
            'additional_commentaire'     => 'nullable|string',
        ]);
    }

    /**
     * Vérifie le stock disponible pour une liste d'articles.
     * Retourne une réponse JSON d'erreur si invalide, sinon null.
     */
    private function checkArticlesStock(array $articles, $user)
    {
        foreach ($articles as $item) {
            $confirmed = Commande::where('commandes.etat_commande', 'CONFIRMED')
                ->join('detailscommandes', 'detailscommandes.id_commande', 'commandes.id_commande')
                ->where(function ($q) use ($user) {
                    $q->where('commandes.id_client', $user->id)
                      ->orWhere('commandes.id_client', $user->superviseur);
                })
                ->where('detailscommandes.id_article', $item['id_article'])
                ->selectRaw('sum(detailscommandes.quantite_article) as qnt_article')
                ->first();

            $stockTotal = Article::where('id_article', $item['id_article'])
                ->value('stock_article');

            $available = $confirmed->qnt_article === null
                ? $stockTotal
                : $stockTotal - $confirmed->qnt_article;

            if ($item['quantite'] <= 0) {
                return response()->json(['message' => 'Erreur la quantité doit etre superieur de 0']);
            }

            if ($item['quantite'] > (int) $available) {
                return response()->json(['message' => 'Erreur la quantité entrer plus que le stock']);
            }
        }

        return null;
    }

    /**
     * Crée une commande et son historique initial.
     */
    private function createCommande(array $data, string $id_commande, $user): bool
    {
        $statut = Commande::create(array_merge($data, [
            'id_commande'  => $id_commande,
            'etat_commande' => 'CREATED',
        ]));

        if ($statut) {
            HistoriqueCommande::create([
                'id_commande'  => $id_commande,
                'etat_commande' => 'CREATED',
                'id_client'    => $user->id,
            ]);
        }

        return (bool) $statut;
    }

    /**
     * Retourne une réponse JSON de succès ou d'erreur selon le booléen.
     */
    private function jsonStatus(bool $success, string $successMsg = 'Successfully', string $errorMsg = 'Erreur')
    {
        return response()->json([
            'message' => $success ? $successMsg : $errorMsg,
        ]);
    }

    // -------------------------------------------------------------------------
    // Actions du contrôleur
    // -------------------------------------------------------------------------

    /**
     * Liste paginée des commandes du client.
     */
    public function index()
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $historiquecommandes = $this->buildHistoriqueSubQuery();

            $commandes = Commande::join('clients', 'commandes.id_client', '=', 'clients.id')
                ->leftJoin('villes', 'commandes.id_ville', '=', 'villes.id')
                ->leftJoin('stores', 'stores.id', '=', 'commandes.id_store')
                ->leftJoin('factures', 'commandes.id_facture', 'factures.id_facture')
                ->leftJoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                    $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                })
                ->where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                ->selectRaw('
                    statut_facture, commandes.id_commande, commandes.nom_client_commande,
                    commandes.type_commande, stores.nom_store, commandes.id_package,
                    commandes.telephone_client_commande, commandes.prix_commande,
                    commandes.etat_commande, commandes.updated_at,
                    villes.nom_ville as ville_client_commande,
                    historiquecommandes.commentaire_commande
                ')
                ->orderBy('commandes.updated_at', 'desc')
                ->paginate(request('count_nbr'));

            return response()->json(['data' => $commandes]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Créer une nouvelle commande (simple, stock, ou import Excel).
     */
    public function store(Request $request)
    {
        $user = $this->authUser();

        // Vérification du solde
        $balance = DB::table('factures')
            ->where('statut_facture', 'NOTPAID')
            ->where('id_client', $user->id)
            ->selectRaw('IFNULL(sum(total_facture - frais_livraison_facture), 0) as balance')
            ->first();

        if ($balance->balance < -1000) {
            return response()->json(['message' => 'Insufficient balance', 'balance' => $balance->balance]);
        }

        // Résolution du store
        $id_store = $this->resolveStoreId($request, $user);
        if ($id_store === false) {
            return response()->json(['message' => "Erreur le store n'est pas de vous"]);
        }

        $type_autorisation = $this->resolveTypeAutorisation($request);

        // Import Excel
        if ($request->typeCommande === 'excel') {
            $this->validate($request, ['fichierCommande' => 'required|mimes:xlsx|max:1000']);
        }

        if ($request->hasFile('fichierCommande')) {
            $import = new CommandesImport($id_store);
            Excel::import($import, $request->file('fichierCommande'));
            return response()->json(['message' => $import->getArray()[0]]);
        }

        if (!$this->isAuthorized($user)) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $nbrCommandeToday = Commande::where('id_client', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($nbrCommandeToday >= 2000) {
            return response()->json(['message' => 'Vous avez saisi un grand nombre de commandes pendant une journée !!']);
        }

        $this->validateCommandeRequest($request);

        $ville         = Ville::find($request->ville_client_commande['id']);
        $id_commande   = $this->generateIdCommande($ville, $user);
        $prix_livraison = $this->resolvePrixLivraison($ville, $user);
        ['id_client' => $id_client, 'id_employe_client' => $id_employe_client] = $this->resolveClientIds($user);

        $baseData = [
            'id_commande_intern'         => $request->id_commande_intern,
            'id_ville'                   => $ville->id,
            'id_store'                   => $id_store,
            'nom_client_commande'        => $request->nom_client_commande,
            'adresse_client_commande'    => $request->adresse_client_commande,
            'telephone_client_commande'  => $request->telephone_client_commande,
            'prix_commande'              => $request->prix_commande,
            'prix_livraison_final'       => $prix_livraison,
            'id_client'                  => $id_client,
            'additional_commentaire'     => $request->additional_commentaire,
            'type_autorisation'          => $type_autorisation,
            'id_employe_client'          => $id_employe_client,
        ];

        // Commande avec stock et articles
        if ($user->stock == 1 && isset($request->articles) && count($request->articles) > 0) {
            $stockError = $this->checkArticlesStock($request->articles, $user);
            if ($stockError) {
                return $stockError;
            }

            $created = $this->createCommande(
                array_merge($baseData, ['type_commande' => 'stock']),
                $id_commande,
                $user
            );

            if ($created) {
                foreach ($request->articles as $article) {
                    DetailsCommandes::create([
                        'id_commande'     => $id_commande,
                        'id_article'      => $article['id_article'],
                        'quantite_article' => $article['quantite'],
                    ]);
                }
            }

            return $this->jsonStatus($created, 'commande created successfully');
        }

        // Commande simple (stock = 0 ou stock = 1 sans articles)
        $created = $this->createCommande($baseData, $id_commande, $user);

        return $this->jsonStatus($created, 'commande created successfully');
    }

    /**
     * Affiche les détails d'une commande avec le stock disponible par article.
     */
    public function show($id)
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $commande = Commande::join('villes', 'commandes.id_ville', '=', 'villes.id')
                ->leftJoin('detailscommandes', 'commandes.id_commande', 'detailscommandes.id_commande')
                ->leftJoin('articles', 'articles.id_article', 'detailscommandes.id_article')
                ->leftJoin('employes', 'commandes.responsable', 'employes.id')
                ->leftJoin('stores', 'commandes.id_store', 'stores.id')
                ->leftJoin('clients', 'clients.id', 'commandes.id_client')
                ->where('commandes.id_commande', $id)
                ->selectRaw('
                    additional_commentaire, telephone_client_commande, adresse_client_commande,
                    etat_commande, type_commande, employes.nom, employes.prenom,
                    employes.telephone as telephone_responsable, stores.nom_store, clients.company,
                    commandes.id_commande, villes.nom_ville, nom_client_commande,
                    prix_commande, commandes.id_package, telephone_client_commande,
                    commandes.prix_livraison_final, type_autorisation, commandes.updated_at,
                    villes.id as ville_client_commande, articles.nom_article
                ')
                ->first();

            $detailsCommandes = DetailsCommandes::leftJoin('articles', 'articles.id_article', 'detailscommandes.id_article')
                ->where('detailscommandes.id_commande', $id)
                ->selectRaw('quantite_article as quantite, articles.id_article as id, articles.nom_article')
                ->get();

            foreach ($detailsCommandes as $detail) {
                $confirmed = Commande::whereIn('commandes.etat_commande', ['CONFIRMED', 'PROCESSING', 'PICKUP', 'INHOUSE'])
                    ->join('detailscommandes', 'detailscommandes.id_commande', 'commandes.id_commande')
                    ->where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                    ->where('detailscommandes.id_article', $detail->id)
                    ->selectRaw('sum(detailscommandes.quantite_article) as qnt_article')
                    ->first();

                $stockTotal = Article::where('id_article', $detail->id)->value('stock_article');
                $available  = $confirmed->qnt_article === null
                    ? $stockTotal
                    : $stockTotal - $confirmed->qnt_article;

                if ($available > 0) {
                    $detail->qnt = $available;
                }
            }

            return response()->json(['data' => $commande, 'data2' => $detailsCommandes]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Met à jour une commande CREATED (données complètes + articles si stock).
     */
    public function updateCommande(Request $request)
    {
        $this->validateCommandeRequest($request);

        $user = $this->authUser();

        try {
            if (!$this->isAuthorized($user, clientOnly: true)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $type_autorisation = $this->resolveTypeAutorisation($request);
            $ville             = Ville::find($request->ville_client_commande['id']);

            // Mise à jour avec articles (mode stock)
            if (isset($request->articles) && count($request->articles) > 0 && $request->selected_type == true) {
                $stockError = $this->checkArticlesStock($request->articles, $user);
                if ($stockError) {
                    return $stockError;
                }

                $existingDetails = DetailsCommandes::where('id_commande', $request->id_commande)->get();

                // Supprimer ou mettre à jour les détails existants
                foreach ($existingDetails as $i => $detail) {
                    $match = collect($request->articles)->firstWhere('id', $detail->id_article);
                    if ($match) {
                        $detail->quantite_article = $request->articles[$i]['quantite'];
                        $detail->id_article       = $request->articles[$i]['id_article'];
                        $statut = $detail->save();
                    } else {
                        DetailsCommandes::where('id_commande', $request->id_commande)
                            ->where('id_article', $detail->id_article)
                            ->delete();
                    }
                }

                // Ajouter les nouveaux articles
                foreach ($request->articles as $newArticle) {
                    $exists = $existingDetails->firstWhere('id_article', $newArticle['id']);
                    if (!$exists) {
                        DetailsCommandes::create([
                            'id_commande'      => $request->id_commande,
                            'id_article'       => $newArticle['id'],
                            'quantite_article' => $newArticle['quantite'],
                        ]);
                    }
                }

                if (!($statut ?? false)) {
                    return $this->jsonStatus(false);
                }
            }

            $commande = Commande::where('id_client', $user->id)
                ->where('etat_commande', 'CREATED')
                ->where('id_commande', $request->id_commande)
                ->first();

            // Regénérer l'id_commande si la ville change (commande simple uniquement)
            if (!($request->selected_type ?? false) && $commande->id_ville != $ville->id) {
                $commande->id_commande = $this->generateIdCommande($ville, $user);
            }

            $commande->id_ville                  = $ville->id;
            $commande->nom_client_commande        = $request->nom_client_commande;
            $commande->id_commande_intern         = $request->id_commande_intern;
            $commande->adresse_client_commande    = $request->adresse_client_commande;
            $commande->telephone_client_commande  = $request->telephone_client_commande;
            $commande->prix_commande              = $request->prix_commande;
            $commande->additional_commentaire     = $request->additional_commentaire;
            $commande->prix_livraison_final       = $this->resolvePrixLivraison($ville, $user);
            $commande->type_autorisation          = $type_autorisation;

            $statut = $commande->save();

            return $this->jsonStatus($statut, 'commande update successfully');
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur' . $e]);
        }
    }

    /**
     * Supprime une commande CREATED.
     */
    public function destroy($id)
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user, clientOnly: true)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $commande = Commande::find($id);

            if ($commande && $commande->etat_commande === 'CREATED') {
                $commande->delete();
                return response()->json(['message' => 'Commande Supprimer']);
            }

            return response()->json(['message' => 'Erreur']);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Retourne les commandes d'un package et génère le PDF (stickers ou standard).
     */
    public function getPackage(Request $request)
    {

        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $baseQuery = Commande::join('clients', 'commandes.id_client', '=', 'clients.id')
                ->leftjoin('villes', 'commandes.id_ville', '=', 'villes.id')
                ->leftjoin('zones', 'zones.id', 'villes.id_zone')
                ->leftJoin('stores', 'stores.id', '=', 'commandes.id_store')
                ->leftjoin('villes as villes2', 'clients.id_ville', '=', 'villes2.id')
                ->leftJoin('villes as store_ville', 'stores.id_ville', '=', 'store_ville.id')
                ->where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                ->where('commandes.id_package', $request->id);

            $commonSelect = [
                'commandes.*',
                'villes.nom_ville as ville',
                'zones.nom_zone',
                'clients.telephone_store',
                'villes2.nom_ville as ville_client',
                'stores.nom_store',
                'stores.siteweb_store',
                'stores.telephone_store as tele_store',
                'stores.adresse_store',
                'store_ville.nom_ville as store_ville',
            ];

            if ($user->role === 'Client') {
                $packageClient = $baseQuery->select(array_merge($commonSelect, [
                    'clients.company',
                    'clients.adresse',
                    'clients.website',
                    'clients.telephone as telephone_client',
                ]))->orderBy('commandes.updated_at', 'desc')->get();
            } else {
                // EmployeClient — joindre le superviseur pour les infos société
                $packageClient = $baseQuery
                    ->join('clients as superviseur', 'superviseur.id', '=', 'commandes.id_client')
                    ->select(array_merge($commonSelect, [
                        'superviseur.company',
                        'superviseur.adresse',
                        'superviseur.website',
                        'superviseur.telephone as telephone_client',
                    ]))->orderBy('commandes.created_at', 'desc')->get();
            }

            $data = ['data' => $packageClient];

            $pdf = isset($request->type) && $request->type === 'smallStickers'
                ? PDF::setOption('page-width', 105)->setOption('page-height', 105)
                     ->setOption('margin-top', 2)->setOption('margin-right', 2)
                     ->setOption('margin-left', 2)->setOption('margin-bottom', 2)
                     ->loadView('miniStickers', $data)
                : PDF::loadView('myPDF', $data)
                     ->setOption('margin-top', 4)->setOption('margin-right', 2)
                     ->setOption('margin-left', 2)->setOption('margin-bottom', 2);

            return $pdf->stream('document.pdf');
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Historique complet d'une commande (événements + factures + livreur).
     */
    public function historiqueCommande($id)
    {
        try {
            $user      = $this->authUser();
            $clientId  = $user->role === 'Client' ? $user->id : $user->superviseur;

            if (!$this->isAuthorized($user)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $commandes2 = DB::table('historiquecommandes')
                ->join('commandes', 'commandes.id_commande', '=', 'historiquecommandes.id_commande')
                ->join('employes', 'historiquecommandes.id_employe', '=', 'employes.id')
                ->where('historiquecommandes.etat_commande', 'HOME')
                ->where('commandes.id_client', $clientId)
                ->where('commandes.id_commande', $id)
                ->select('employes.telephone')
                ->first();

            $livreur = DB::table('commandes')
                ->join('employes', 'commandes.livre_par', '=', 'employes.id')
                ->where('commandes.id_commande', $id)
                ->select('employes.telephone', 'employes.nom', 'employes.prenom')
                ->first();

            $commandes = DB::table('historiquecommandes')
                ->leftJoin('employes', 'historiquecommandes.id_employe', '=', 'employes.id')
                ->leftJoin('clients', 'historiquecommandes.id_client', '=', 'clients.id')
                ->join('commandes', 'commandes.id_commande', '=', 'historiquecommandes.id_commande')
                ->join('villes', 'commandes.id_ville', 'villes.id')
                ->leftJoin('zones', 'zones.id', 'villes.id_zone')
                ->where('historiquecommandes.id_commande', $id)
                ->where('commandes.id_client', $clientId)
                ->select(
                    'zones.nom_zone', 'commandes.id_bon_retour_client', 'commandes.id_package',
                    'historiquecommandes.etat_commande', 'commandes.nom_client_commande',
                    'historiquecommandes.dateCall', 'historiquecommandes.typeCall',
                    'historiquecommandes.durationCall', 'villes.nom_ville',
                    'clients.nom as clientUsername', 'historiquecommandes.reported_date',
                    'historiquecommandes.commentaire_commande', 'employes.nom as username',
                    'historiquecommandes.updated_at'
                )
                ->orderBy('historiquecommandes.updated_at', 'asc')
                ->get();

            $historiquefactures = DB::table('historiquefactures')
                ->leftJoin('employes', 'historiquefactures.id_employe', '=', 'employes.id')
                ->join('commandes', 'commandes.id_facture', '=', 'historiquefactures.id_facture')
                ->where('commandes.id_commande', $id)
                ->where('commandes.id_client', $clientId)
                ->select(
                    'historiquefactures.statut_facture',
                    'historiquefactures.id_facture',
                    'employes.nom as username',
                    'historiquefactures.updated_at'
                )
                ->orderBy('historiquefactures.updated_at', 'asc')
                ->get();

            return response()->json([
                'data'        => $commandes,
                'responsable' => $commandes2,
                'data2'       => $historiquefactures,
                'livreur'     => $livreur,
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Change le statut d'une commande (ANNULER / RELANCER / CHANGERPRIX).
     */
    public function changeStatutCommande(Request $request)
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            if (!in_array($request->statut, ['ANNULER', 'RELANCER', 'CHANGERPRIX'])) {
                return response()->json(['message' => 'Erreur']);
            }

            $commande = Commande::where('id_commande', $request->id_commande)
                ->whereIn('etat_commande', [
                    'HOME', 'TRANSIT', 'RELANCER', 'CHANGERPRIX', 'INHOUSE',
                    'REPORTED', 'NOREPONSE', 'PROCESSING', 'ASSIGN',
                    'ENROUTE', 'PICKUP', 'DMSUIVIE', 'RAMASSER',
                ])
                ->where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                ->first();

            if (!$commande) {
                return response()->json(['message' => 'Erreur']);
            }

            $commentaire_commande = null;
            $reported_date        = null;

            if ($request->dateReported != '') {
                $reported_date = Carbon::parse($request->dateReported);
            }

            if ($request->statut === 'ANNULER') {
                $commentaire_commande  = 'Order Cancel';
                $commande->etat_commande = 'ANNULER';
            } elseif ($request->statut === 'RELANCER') {
                $commentaire_commande  = 'Relaunch request';
                $commande->etat_commande = 'RELANCER';
            }

            $historique = HistoriqueCommande::create([
                'id_commande'          => $commande->id_commande,
                'etat_commande'        => $request->statut,
                'commentaire_commande' => $commentaire_commande,
                'reported_date'        => $reported_date,
                'id_client'            => $user->id,
            ]);

            if ($historique && $request->statut === 'RELANCER') {
                Notification::create([
                    'id_commande' => $commande->id_commande,
                    'description' => 'Demande Relance: ' . $commande->id_commande,
                    'titre'       => 'Demande Relance',
                    'affichage'   => 'notSeen',
                    'id_client'   => $user->id,
                    'id_employe'  => $commande->livre_par,
                ]);
            }

            $commande->updated_at = Carbon::now();
            $commande->save();

            return $this->jsonStatus((bool) $historique);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Liste paginée des commandes en suivi (état DMSUIVIE).
     */
    public function getCommandeSuivie(Request $request)
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $historiquecommandes = $this->buildHistoriqueSubQuery();

            $commandes = Commande::join('clients', 'commandes.id_client', '=', 'clients.id')
                ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                ->leftJoin('stores', 'stores.id', '=', 'commandes.id_store')
                ->leftJoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                    $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                })
                ->where('commandes.etat_commande', 'DMSUIVIE')
                ->where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                ->selectRaw('
                    commandes.adresse_client_commande, commandes.id_commande,
                    commandes.nom_client_commande, commandes.type_commande,
                    stores.nom_store, clients.company, commandes.telephone_client_commande,
                    commandes.prix_commande, commandes.etat_commande, commandes.updated_at,
                    villes.nom_ville as ville_client_commande,
                    historiquecommandes.commentaire_commande
                ')
                ->orderBy('commandes.updated_at', 'desc')
                ->paginate(request('count_nbr'));

            return response()->json(['data' => $commandes]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Liste paginée des bons de retour du client.
     */
    public function getBonRetour(Request $request)
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user, clientOnly: true)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $query = DB::table('bonretourclients')->where('id_client', $user->id);

            if ($request->selected_option === 'id_bon_retour_client' && $request->valeur_recherche != '') {
                $query->where('id_bon_retour_client', 'LIKE', "%{$request->valeur_recherche}%")
                      ->selectRaw('id_bon_retour_client, statut_bonRetourClient, nbrColis_bonRetourClient, updated_at');
            } elseif ($request->selected_option === 'id_commande' && $request->valeur_recherche != '') {
                $query->join('commandes', 'commandes.id_bon_retour_client', 'bonretourclients.id_bon_retour_client')
                      ->where('commandes.id_commande', 'LIKE', "%{$request->valeur_recherche}%")
                      ->selectRaw('bonretourclients.id_bon_retour_client, bonretourclients.statut_bonRetourClient, bonretourclients.nbrColis_bonRetourClient, bonretourclients.updated_at');
            } else {
                $query->selectRaw('id_bon_retour_client, statut_bonRetourClient, nbrColis_bonRetourClient, updated_at');
            }

            $commandes = $query->orderBy('bonretourclients.updated_at', 'desc')->paginate(request('count_nbr'));

            return response()->json(['data' => $commandes]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur' . $e]);
        }
    }

    /**
     * Téléchargement du PDF d'un bon de retour depuis S3.
     */
    public function getBonRetourClient($id)
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user, clientOnly: true)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $bon = DB::table('bonretourclients')
                ->where('id_client', $user->id)
                ->where('id_bon_retour_client', $id)
                ->selectRaw('id_bon_retour_client, statut_bonRetourClient, nbrColis_bonRetourClient')
                ->first();

            return Storage::disk('s3')->download(
                'public/Bons/BonRetourClient/' . $bon->id_bon_retour_client . '.pdf'
            );
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Confirme la réception d'un retour (RETURNEDRR → RETURNED).
     */
    public function receptionRetour($id)
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user, clientOnly: true)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $commande = Commande::where('id_commande', $id)
                ->where('id_client', $user->id)
                ->where('etat_commande', 'RETURNEDRR')
                ->firstOrFail();

            $commande->etat_commande = 'RETURNED';
            $saved = $commande->save();

            if (!$saved) {
                return $this->jsonStatus(false);
            }

            $historique = HistoriqueCommande::create([
                'id_commande'          => $commande->id_commande,
                'etat_commande'        => 'RETURNED',
                'commentaire_commande' => 'Return received by ' . $user->username,
                'id_client'            => $user->id,
            ]);

            return $this->jsonStatus((bool) $historique, 'Commande RETURNED Successfully');
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Vérifie si une commande a déjà été relancée.
     */
    public function verificationRelaunch($id)
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $exists = HistoriqueCommande::where('id_commande', $id)
                ->where(fn($q) => $this->scopeClientOrSupervisor(
                    $q->select('*')->from('historiquecommandes')
                        ->whereColumn('historiquecommandes.id_commande', 'historiquecommandes.id_commande'),
                    $user
                ))
                ->where('etat_commande', 'RELANCER')
                ->exists();

            // Note: la logique originale filtre par id_client directement
            $statut = HistoriqueCommande::where('id_commande', $id)
                ->where(function ($q) use ($user) {
                    $q->where('id_client', $user->id)
                      ->orWhere('id_client', $user->superviseur);
                })
                ->where('etat_commande', 'RELANCER')
                ->first();

            return response()->json([
                'message' => $statut ? 'Order already relaunch' : 'Order not relaunched',
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Marque un package comme imprimé.
     */
    public function changeStatusToPrint($id)
    {
        $user = $this->authUser();

        if (!$this->isAuthorized($user)) {
            return;
        }

        $package = Package::join('commandes', 'commandes.id_package', 'packages.id_package')
            ->where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
            ->where('packages.id_package', $id)
            ->first();

        if ($package) {
            $package->statut_package = 'Printed';
            $package->save();
        }
    }

    /**
     * Mise à jour partielle d'une commande (nom, adresse, téléphone, prix).
     */
    public function updateCommandeInfo(Request $request)
    {
        $this->validate($request, [
            'adresse_client_commande'   => 'required|string',
            'nom_client_commande'       => 'required|string',
            'telephone_client_commande' => 'required|regex:/(0)[0-9]{9}$/',
            'prix_commande'             => 'required|numeric',
        ]);

        $user = $this->authUser();

        try {
            if (!$this->isAuthorized($user)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $commande = Commande::where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                ->whereNotIn('etat_commande', [
                    'DELIVERED', 'CANCEL', 'ANNULER', 'RETURNED',
                    'RETURNEDEV', 'RETURNEDLV', 'RETURNEDAG', 'RETURNEDRR',
                ])
                ->where('id_commande', $request->id_commande)
                ->first();

            $etat_commande        = 'COMMENTAIRE';
            $commentaire_commande = 'Changement de destination';

            if ($request->nom_client_commande != $commande->nom_client_commande) {
                $commande->nom_client_commande = $request->nom_client_commande;
            } elseif ($request->prix_commande != $commande->prix_commande) {
                if ($request->prix_commande < 0) {
                    return response()->json(['message' => 'Le prix doit etre superieur ou egale 0']);
                }

                if ($request->prix_commande >= $commande->prix_commande) {
                    return response()->json(['message' => 'Le prix doit être inférieur au prix précédent']);
                }

                $etat_commande        = 'CHANGERPRIX';
                $commentaire_commande = 'Changement de prix de ' . $commande->prix_commande . ' à (' . $request->prix_commande . ' Dhs)';
                $commande->prix_commande = $request->prix_commande;

                // Conserver l'état actuel si en cours de traitement/livraison
                if (!in_array($commande->etat_commande, ['PROCESSING', 'PICKUP', 'ENROUTE'])) {
                    $commande->etat_commande = $etat_commande;
                }
            } elseif ($request->adresse_client_commande != $commande->adresse_client_commande) {
                $commande->adresse_client_commande = $request->adresse_client_commande;
            } elseif ($request->telephone_client_commande != $commande->telephone_client_commande) {
                $commande->telephone_client_commande = $request->telephone_client_commande;
            } else {
                return response()->json(['message' => 'No Change']);
            }

            $statut = $commande->save();

            if ($statut) {
                HistoriqueCommande::create([
                    'id_commande'          => $commande->id_commande,
                    'etat_commande'        => $etat_commande,
                    'commentaire_commande' => $commentaire_commande,
                    'id_client'            => $user->id,
                ]);

                return response()->json(['message' => 'commande update successfully']);
            }

            return response()->json(['message' => 'Erreur']);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }
}
