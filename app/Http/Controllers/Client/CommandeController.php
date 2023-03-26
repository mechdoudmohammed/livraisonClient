<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\Models\Commande;
use App\Models\Article;
use App\Models\HistoriqueCommande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CommandesImport;
use App\Models\DetailsCommandes;
use App\Models\Notification;
use App\Models\Store;
use App\Models\Ville;
use Laravel\Sanctum\PersonalAccessToken;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Carbon\Carbon;
use Hamcrest\Type\IsString;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CommandeController extends Controller
{
    public function index()
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
                $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                    ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                    ->groupBy('id_commande');
                $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                    ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                    ->join('stores', 'stores.id', '=', 'commandes.id_store')
                    ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                    ->where(function ($query) use ($user) {
                        $query->where('commandes.id_client', $user->id)
                            ->orwhere('commandes.id_client', $user->superviseur);
                    })
                    ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                        $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                    })
                    ->selectRaw('statut_facture,commandes.id_commande,commandes.nom_client_commande,commandes.type_commande,stores.nom_store,	
                    commandes.telephone_client_commande,commandes.prix_commande,commandes.etat_commande,commandes.updated_at,villes.nom_ville as ville_client_commande,historiquecommandes.commentaire_commande')
                    ->orderBy('commandes.updated_at', 'desc')
                    ->paginate($_GET['count_nbr']);
                return response()->json([
                    'data' => $commandes
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function store(Request $request)
    {

        $user = auth('sanctum')->user();
        if (isset($request->store)) {

            if (Is_string($request->store)) {

                $store = Store::where('id', $request->store)->where(function ($query) use ($user) {
                    $query->where('id_client', $user->id)
                        ->orwhere('id_client', $user->superviseur);
                })->first();
                if (!$store) {
                    return response()->json([
                        'message' => 'Erreur le store n\'est pas de vous'
                    ]);
                } else {
                    $id_store = $store->id;
                }
            } else {
                $store = Store::where('id', $request->store['id'])->where(function ($query) use ($user) {
                    $query->where('id_client', $user->id)
                        ->orwhere('id_client', $user->superviseur);
                })->first();

                if (!$store) {
                    return response()->json([
                        'message' => 'Erreur le store n\'est pas de vous'
                    ]);
                } else {
                    $id_store = $request->store['id'];
                }
            }
        } else {
            $id_store = null;
        }
        if (isset($request->type_autorisation)) {
            if ($request->type_autorisation) {
                $type_autorisation = 'deny';
            } else {
                $type_autorisation = 'allow';
            }
        } else {
            $type_autorisation = 'allow';
        }
        if (isset($request->typeCommande) && $request->typeCommande == 'excel') {
            $this->validate($request, [
                'fichierCommande' => 'required|mimes:xlsx|max:1000',
            ]);
        }
        if ($request->hasFile('fichierCommande')) {


            $import = new CommandesImport($id_store);
            Excel::import($import, request()->file('fichierCommande'));
            $array = $import->getArray();
            
            return response()->json([
                'message' => $array[0]
            ]);
        }
        if (($user->role == 'Client' || $user->role == 'EmployeClient') && $user->statut == 'Active') {
            $nbrCommandeToday = Commande::where('id_client', $user->id)->whereDate('created_at', Carbon::today())->count();
            if ($nbrCommandeToday < 2000) {

                $id_employe_client = null;
                if ($user->stock == 0) {
                    $this->validate($request, [
                        "ville_client_commande" => 'required',
                        "nom_client_commande" => "required|string",
                        "adresse_client_commande" => 'required|string',
                        "telephone_client_commande" => "required|regex:/(0)[0-9]{9}/",
                        "prix_commande" => "required|numeric",
                        "additional_commentaire" => "nullable|string",
                    ]);

                    $ville = Ville::where('id', $request->ville_client_commande['id'])->first();
                    $id_commande = $ville->pref_ville . strtoupper(Str::random(6)) . time();

                    if ($user->role == 'EmployeClient') {
                        $id_client = $user->superviseur;
                        $id_employe_client = $user->id;
                    } else if ($user->role == 'Client') {
                        $id_client = $user->id;
                    }
                    $statut = Commande::create([
                        "id_commande" =>  $id_commande,
                        "id_ville" => $request->ville_client_commande['id'],
                        "id_store" => $id_store,
                        "nom_client_commande" => $request->nom_client_commande,
                        "adresse_client_commande" => $request->adresse_client_commande,
                        "telephone_client_commande" => $request->telephone_client_commande,
                        "prix_commande" => $request->prix_commande,
                        "prix_livraison_final" => $ville->prix_livraison,
                        "etat_commande" => "CREATED",
                        "id_client" => $id_client,
                        "additional_commentaire" => $request->additional_commentaire,
                        "type_autorisation" => $type_autorisation,
                        'id_employe_client' => $id_employe_client,

                    ]);
                    HistoriqueCommande::create([
                        "id_commande" =>  $id_commande,
                        "etat_commande" => 'CREATED',
                        "id_client" => $user->id,
                    ]);
                    if ($statut) {
                        return response()->json([
                            'message' => 'commande created successfully'
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Erreur'
                        ]);
                    }
                } elseif ($user->stock == 1) {
                    if (isset($request->articles) && count($request->articles) > 0) {
                        $this->validate($request, [
                            "ville_client_commande" => 'required',
                            "nom_client_commande" => "required|string",
                            "adresse_client_commande" => 'required|string',
                            "telephone_client_commande" => "required|regex:/(0)[0-9]{9}/",
                            "prix_commande" => "required|numeric",
                            "additional_commentaire" => "nullable|string",
                        ]);
                        for ($i = 0; $i < count($request->articles); $i++) {
                            //pour savoir le nombre des articles deja confirmer
                            $article = Commande::where('commandes.etat_commande', 'CONFIRMED')
                                ->join('detailscommandes', 'detailscommandes.id_commande', 'commandes.id_commande')
                                ->where(function ($query) use ($user) {
                                    $query->where('commandes.id_client', $user->id)
                                        ->orwhere('commandes.id_client', $user->superviseur);
                                })
                                ->where('detailscommandes.id_article', $request->articles[$i]['id'])
                                ->selectRaw('sum(detailscommandes.quantite_article) as qnt_article')
                                ->first();
                            // that if means it's the first confirmed of the article
                            if ($article->qnt_article == null) {
                                $article_in_stock = Article::where('articles.id', $request->articles[$i]['id'])
                                    ->selectRaw('articles.stock_article as qnt_article_stock')
                                    ->first();
                                $articles = $article_in_stock->qnt_article_stock;
                            } else {
                                //pour savoir la quantite exist en stock
                                $article_in_stock = Article::where('articles.id', $request->articles[$i]['id'])
                                    ->selectRaw('articles.stock_article as qnt_article_stock')
                                    ->first();
                                $articles = $article_in_stock->qnt_article_stock - $article->qnt_article;
                            }
                            if ($request->articles[$i]['quantite'] <= 0) {
                                return response()->json([
                                    'message' => 'Erreur la quantité doit etre superieur de 0'
                                ]);
                            } elseif ($request->articles[$i]['quantite'] > (int)$articles) {
                                return response()->json([
                                    'message' => 'Erreur la quantité entrer plus que le stock'
                                ]);
                            }
                        }
                        if ($user->role == 'EmployeClient') {
                            $id_client = $user->superviseur;
                            $id_employe_client = $user->id;
                        } else if ($user->role == 'Client') {
                            $id_client = $user->id;
                        }
                        $ville = Ville::where('id', $request->ville_client_commande['id'])->first();
                        $id_commande = $ville->pref_ville . strtoupper(Str::random(6)) . time();
                        $statut = Commande::create([
                            "id_commande" => $id_commande,
                            "id_ville" => $request->ville_client_commande['id'],
                            "nom_client_commande" => $request->nom_client_commande,
                            "adresse_client_commande" => $request->adresse_client_commande,
                            "telephone_client_commande" => $request->telephone_client_commande,
                            "prix_commande" => $request->prix_commande,
                            "etat_commande" => "CREATED",
                            "id_client" => $id_client,
                            "prix_livraison_final" => $ville->prix_livraison,
                            "additional_commentaire" => $request->additional_commentaire,
                            "type_autorisation" => $type_autorisation,
                            "type_commande" => 'stock',
                            "id_store" => $id_store,
                            'id_employe_client' => $id_employe_client,

                        ]);
                        HistoriqueCommande::create([
                            "id_commande" =>  $id_commande,
                            "etat_commande" => 'CREATED',
                            "id_client" => $user->id,
                        ]);
                        for ($i = 0; $i < count($request->articles); $i++) {
                            DetailsCommandes::create([
                                "id_commande" =>  $id_commande,
                                "id_article" => $request->articles[$i]['id'],
                                "quantite_article" => $request->articles[$i]['quantite'],
                            ]);
                        }
                        if ($statut) {
                            return response()->json([
                                'message' => 'commande created successfully'
                            ]);
                        } else {
                            return response()->json([
                                'message' => 'Erreur'
                            ]);
                        }
                    } else {
                        $this->validate($request, [
                            "ville_client_commande" => 'required',
                            "nom_client_commande" => "required|string",
                            "adresse_client_commande" => 'required|string',
                            "telephone_client_commande" => "required|regex:/(0)[0-9]{9}/",
                            "prix_commande" => "required|numeric",
                            "additional_commentaire" => "nullable|string",

                        ]);
                        if ($user->role == 'EmployeClient') {
                            $id_client = $user->superviseur;
                            $id_employe_client = $user->id;
                        } else if ($user->role == 'Client') {
                            $id_client = $user->id;
                        }
                        $ville = Ville::where('id', $request->ville_client_commande['id'])->first();
                        $id_commande = $ville->pref_ville . strtoupper(Str::random(6)) . time();
                        $statut = Commande::create([
                            "id_commande" => $id_commande,
                            "id_ville" => $request->ville_client_commande['id'],
                            "nom_client_commande" => $request->nom_client_commande,
                            "adresse_client_commande" => $request->adresse_client_commande,
                            "telephone_client_commande" => $request->telephone_client_commande,
                            "prix_commande" => $request->prix_commande,
                            "prix_livraison_final" => $ville->prix_livraison,
                            "etat_commande" => "CREATED",
                            "id_client" => $id_client,
                            "type_autorisation" => $type_autorisation,
                            "additional_commentaire" => $request->additional_commentaire,
                            "id_store" => $id_store,
                            'id_employe_client' => $id_employe_client,
                        ]);
                        HistoriqueCommande::create([
                            "id_commande" =>  $id_commande,
                            "etat_commande" => 'CREATED',
                            "id_client" => $user->id,
                        ]);
                        if ($statut) {
                            return response()->json([
                                'message' => 'commande created successfully'
                            ]);
                        } else {
                            return response()->json([
                                'message' => 'Erreur'
                            ]);
                        }
                    }
                }
            } else {
                return response()->json([
                    'message' => 'Vous avez saisi un grand nombre de commandes pendant une journée !!'
                ]);
            }
        }
    }
    public function show($id)
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
                $commande = Commande::join('villes', 'commandes.id_ville', '=', 'villes.id')
                    ->leftjoin('detailscommandes', 'commandes.id_commande', 'detailscommandes.id_commande')
                    ->leftjoin('articles', 'articles.id', 'detailscommandes.id_article')
                    ->leftjoin('employes', 'commandes.responsable', 'employes.id')
                    ->leftjoin('stores', 'commandes.id_store', 'stores.id')
                    ->leftjoin('clients', 'clients.id', 'commandes.id_client')
                    ->where('commandes.id_commande', $id)
                    ->selectRaw('additional_commentaire,telephone_client_commande,adresse_client_commande,etat_commande,type_commande,employes.nom,employes.prenom,employes.telephone as telephone_responsable,stores.nom_store,clients.company,
                commandes.id_commande,villes.nom_ville,nom_client_commande,prix_commande,
                telephone_client_commande,commandes.prix_livraison_final,
                type_autorisation,commandes.updated_at,villes.id as ville_client_commande,articles.nom_article')
                    ->first();
                $DetailsCommandes = DetailsCommandes::leftjoin('articles', 'articles.id', 'detailscommandes.id_article')
                    ->where('detailscommandes.id_commande', $id)
                    ->selectRaw('quantite_article as quantite,articles.id as id,articles.nom_article')
                    ->get();
                for ($i = 0; $i < count($DetailsCommandes); $i++) {
                    $article = Commande::whereIn('commandes.etat_commande', ['CONFIRMED', 'PROCESSING', 'PICKUP', 'INHOUSE'])
                        ->join('detailscommandes', 'detailscommandes.id_commande', 'commandes.id_commande')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->where('detailscommandes.id_article', $DetailsCommandes[$i]->id)
                        ->selectRaw('sum(detailscommandes.quantite_article) as qnt_article')
                        ->first();
                    // that if means it's the first confirmed of the article
                    if ($article->qnt_article == null) {
                        $article_in_stock = Article::where('articles.id', $DetailsCommandes[$i]->id)
                            ->selectRaw('articles.stock_article as qnt_article_stock')
                            ->first();
                        if ($article_in_stock->qnt_article_stock <= 0) {
                        } else {
                            $DetailsCommandes[$i]->qnt = $article_in_stock->qnt_article_stock;
                        }
                    } else {
                        //pour savoir la quantite exist en stock
                        $article_in_stock = Article::where('articles.id', $DetailsCommandes[$i]->id)
                            ->selectRaw('articles.stock_article as qnt_article_stock')
                            ->first();
                        if ($article_in_stock->qnt_article_stock - $article->qnt_article <= 0) {
                        } else {
                            $DetailsCommandes[$i]->qnt = $article_in_stock->qnt_article_stock - $article->qnt_article;
                        }
                    }
                }


                return response()->json([
                    'data' => $commande,
                    'data2' => $DetailsCommandes
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function updateCommande(Request $request)
    {

        $this->validate($request, [
            "ville_client_commande" => 'required',
            "nom_client_commande" => "required|string",
            "adresse_client_commande" => 'required|string',
            "telephone_client_commande" => "required|regex:/(0)[0-9]{9}/",
            "prix_commande" => "required|numeric",
            "additional_commentaire" => "nullable|string",
        ]);
        $user = auth('sanctum')->user();
        try {
            if ($user->role == 'Client' && $user->statut == 'Active') {
                if (isset($request->type_autorisation)) {
                    if ($request->type_autorisation) {
                        $type_autorisation = 'deny';
                    } else {
                        $type_autorisation = 'allow';
                    }
                } else {
                    $type_autorisation = 'allow';
                }

                $ville = Ville::where('id', $request->ville_client_commande['id'])->first();

                if (isset($request->articles) && count($request->articles) > 0 && $request->selected_type == true) {
                    //pour savoir le nombre des articles deja confirmer
                    for ($i = 0; $i < count($request->articles); $i++) {
                        //pour savoir le nombre des articles deja confirmer
                        $article = Commande::where('commandes.etat_commande', 'CONFIRMED')
                            ->join('detailscommandes', 'detailscommandes.id_commande', 'commandes.id_commande')
                            ->where(function ($query) use ($user) {
                                $query->where('commandes.id_client', $user->id)
                                    ->orwhere('commandes.id_client', $user->superviseur);
                            })
                            ->where('detailscommandes.id_article', $request->articles[$i]['id'])
                            ->selectRaw('sum(detailscommandes.quantite_article) as qnt_article')
                            ->first();
                        // that if means it's the first confirmed of the article
                        if ($article->qnt_article == null) {
                            $article_in_stock = Article::where('articles.id', $request->articles[$i]['id'])
                                ->selectRaw('articles.stock_article as qnt_article_stock')
                                ->first();
                            $articles = $article_in_stock->qnt_article_stock;
                        } else {
                            //pour savoir la quantite exist en stock
                            $article_in_stock = Article::where('articles.id', $request->articles[$i]['id'])
                                ->selectRaw('articles.stock_article as qnt_article_stock')
                                ->first();
                            $articles = $article_in_stock->qnt_article_stock - $article->qnt_article;
                        }
                        if ($request->articles[$i]['quantite'] <= 0) {
                            return response()->json([
                                'message' => 'Erreur la quantité doit etre superieur de 0'
                            ]);
                        } elseif ($request->articles[$i]['quantite'] > (int)$articles) {
                            return response()->json([
                                'message' => 'Erreur la quantité entrer plus que le stock'
                            ]);
                        }
                    }

                    $detailscommandes = DetailsCommandes::where('detailscommandes.id_commande', $request->id_commande)->get();

                    for ($i = 0; $i < count($detailscommandes); $i++) {
                        $exite_article = false;
                        for ($n = 0; $n < count($request->articles); $n++) {
                            if ($detailscommandes[$i]->id_article == $request->articles[$n]['id']) {
                                $exite_article = true;
                            }
                        }
                        if ($exite_article) {
                            $detailscommandes[$i]->quantite_article = $request->articles[$i]['quantite'];
                            $detailscommandes[$i]->id_article = $request->articles[$i]['id'];
                            $statut = $detailscommandes[$i]->save();
                        } else {
                            DetailsCommandes::where('detailscommandes.id_commande', $request->id_commande)->where('detailscommandes.id_article', $detailscommandes[$i]->id_article)->delete();
                        }
                    }

                    for ($j = 0; $j < count($request->articles); $j++) {
                        $exite_article2 = false;
                        for ($k = 0; $k < count($detailscommandes); $k++) {
                            if ($detailscommandes[$k]->id_article == $request->articles[$j]['id']) {
                                $exite_article2 = true;
                            }
                        }
                        if (!$exite_article2) {
                            DetailsCommandes::create([
                                "id_commande" =>  $request->id_commande,
                                "id_article" => $request->articles[$j]['id'],
                                "quantite_article" => $request->articles[$j]['quantite'],
                            ]);
                        }
                    }

                    if ($statut) {

                        $commande = Commande::where('commandes.id_client', $user->id)->where('etat_commande', 'CREATED')->where('id_commande', $request->id_commande)->first();
                        $commande->id_ville =  $ville->id;
                        $commande->nom_client_commande = $request->nom_client_commande;
                        $commande->adresse_client_commande = $request->adresse_client_commande;
                        $commande->telephone_client_commande = $request->telephone_client_commande;
                        $commande->prix_commande = $request->prix_commande;
                        $commande->additional_commentaire = $request->additional_commentaire;
                        $commande->prix_livraison_final = $ville->prix_livraison;
                        $commande->type_autorisation = $type_autorisation;
                        $statut = $commande->save();
                    }



                    if ($statut) {
                        return response()->json([
                            'message' => 'commande update successfully'
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Erreur'
                        ]);
                    }
                } else {

                    $commande = Commande::where('commandes.id_client', $user->id)->where('etat_commande', 'CREATED')->where('id_commande', $request->id_commande)->first();
                    $commande->id_ville =  $ville->id;
                    $commande->nom_client_commande = $request->nom_client_commande;
                    $commande->adresse_client_commande = $request->adresse_client_commande;
                    $commande->telephone_client_commande = $request->telephone_client_commande;
                    $commande->prix_commande = $request->prix_commande;
                    $commande->additional_commentaire = $request->additional_commentaire;
                    $commande->prix_livraison_final = $ville->prix_livraison;
                    $commande->type_autorisation = $type_autorisation;
                    $statut = $commande->save();
                    if ($statut) {
                        return response()->json([
                            'message' => 'commande update successfully'
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Erreur'
                        ]);
                    }
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function destroy($id)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $commande = Commande::find($id);
                if ($commande->etat_commande == 'CREATED') {
                    $commande->delete();
                    return response()->json(['message' => "Commande Supprimer"]);
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function getPackage(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
                if ($user->role == 'Client') {
                    $packageClient = DB::table('commandes')
                        ->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->join('villes as villes2', 'clients.id_ville', '=', 'villes2.id')
                        ->leftjoin('villes as store_ville', 'stores.id_ville', '=', 'store_ville.id')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->where('commandes.id_package', $request->id)
                        ->select(
                            'commandes.*',
                            'villes.nom_ville as ville',
                            'clients.company',
                            'clients.adresse',
                            'clients.website',
                            'clients.telephone as telephone_client',
                            'villes2.nom_ville as ville_client',
                            'stores.nom_store',
                            'stores.siteweb_store',
                            'stores.telephone_store',
                            'stores.adresse_store',
                            'store_ville.nom_ville as store_ville'

                        )
                        ->orderBy('commandes.updated_at', 'desc')
                        ->get();
                } else if ($user->role == 'EmployeClient') {
                    $packageClient = DB::table('commandes')
                        ->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('clients as superviseur', 'superviseur.id', '=', 'commandes.id_client')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->join('villes as villes2', 'clients.id_ville', '=', 'villes2.id')
                        ->leftjoin('villes as store_ville', 'stores.id_ville', '=', 'store_ville.id')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->where('commandes.id_package', $request->id)
                        ->select(
                            'commandes.*',
                            'villes.nom_ville as ville',
                            'superviseur.company',
                            'superviseur.adresse',
                            'superviseur.website',
                            'superviseur.telephone as telephone_client',
                            'villes2.nom_ville as ville_client',
                            'stores.nom_store',
                            'stores.siteweb_store',
                            'stores.telephone_store',
                            'stores.adresse_store',
                            'store_ville.nom_ville as store_ville'

                        )
                        ->orderBy('.commandes.updated_at', 'desc')
                        ->get();
                }
                $data = ['data' => $packageClient];

                if (isset($request->type) && $request->type == 'smallStickers') {
                    $pdf = PDF::setOption('page-width', 105)
                        ->setOption('page-height', 105)

                        ->setOption('margin-top', 1)
                        ->setOption('margin-right', 1)
                        ->setOption('margin-left', 1)
                        ->setOption('margin-bottom', 1)

                        ->loadView('miniStickers', $data);
                } else {
                    $pdf = PDF::loadView('myPDF', $data)->setOption('margin-top', 4)
                        ->setOption('margin-right', 2)
                        ->setOption('margin-left', 2)
                        ->setOption('margin-bottom', 2);
                }




                return $pdf->stream('document.pdf');
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function historiqueCommande($id)
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
                $commandes2 = DB::table('historiquecommandes')
                    ->join('commandes', 'commandes.id_commande', '=', 'historiquecommandes.id_commande')
                    ->join('employes', 'historiquecommandes.id_employe', '=', 'employes.id')
                    ->where('historiquecommandes.etat_commande', 'HOME')
                    ->where('commandes.id_client', $user->id)
                    ->where('commandes.id_commande', $id)
                    ->select('employes.telephone')->first();
                $livreur = DB::table('commandes')
                    ->join('employes', 'commandes.livre_par', '=', 'employes.id')
                    ->where('commandes.id_commande', $id)
                    ->select('employes.telephone', 'employes.nom', 'employes.prenom')->first();
                $commandes = DB::table('historiquecommandes')
                    ->leftjoin('employes', 'historiquecommandes.id_employe', '=', 'employes.id')
                    ->leftjoin('clients', 'historiquecommandes.id_client', '=', 'clients.id')
                    ->join('commandes', 'commandes.id_commande', '=', 'historiquecommandes.id_commande')

                    ->join('villes', 'commandes.id_ville', 'villes.id')
                    ->where('historiquecommandes.id_commande', $id)
                    ->where('commandes.id_client', $user->id)
                    ->select('historiquecommandes.etat_commande', 'commandes.nom_client_commande', 'historiquecommandes.dateCall', 'historiquecommandes.typeCall', 'historiquecommandes.durationCall', 'villes.nom_ville', 'clients.username as clientUsername', 'historiquecommandes.reported_date', 'historiquecommandes.commentaire_commande', 'employes.username', 'historiquecommandes.updated_at',)
                    ->orderBy('historiquecommandes.updated_at', 'asc')
                    ->get();

                $historiquefactures = DB::table('historiquefactures')
                    ->leftjoin('employes', 'historiquefactures.id_employe', '=', 'employes.id')
                    ->join('commandes', 'commandes.id_facture', '=', 'historiquefactures.id_facture')
                    ->where('commandes.id_commande', $id)
                    ->where('commandes.id_client', $user->id)
                    ->select('historiquefactures.statut_facture', 'employes.username', 'historiquefactures.updated_at',)
                    ->orderBy('historiquefactures.updated_at', 'asc')
                    ->get();

                return response()->json([
                    'data' => $commandes,
                    'responsable' => $commandes2,
                    'data2' => $historiquefactures,
                    'livreur' => $livreur
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function changeStatutCommande(Request $request)
    {
        $this->validate($request, []);
        try {

            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
                if (in_array($request->statut, array('ANNULER', 'RELANCER', 'CHANGERPRIX'))) {
                    $statut = $commande = Commande::where('commandes.id_commande', $request->id_commande)
                        ->whereIn('etat_commande', ['HOME', 'TRANSIT', 'RELANCER', 'INHOUSE', 'REPORTED', 'NOREPONSE', 'ASSIGN', 'ENROUTE', 'PICKUP', 'DMSUIVIE', 'RAMASSER'])
                        ->first();
                }
                if ($statut) {

                    $commentaire_commande = null;
                    $reported_date = null;
                    if ($request->dateReported != '') {
                        $reported_date = Carbon::parse($request->dateReported);
                    }
                    if ($request->statut == 'ANNULER') {
                        $commentaire_commande = 'Commande Annuler par ' . $user->username;
                        $commande->etat_commande = $request->statut;
                    } else if ($request->statut == 'CHANGERPRIX') {
                        if ($request->commentaire_commande < 0) {
                            return response()->json([
                                'message' => 'Le prix doit etre superieur ou egale 0',
                            ]);
                        } else if ($request->commentaire_commande >= $commande->prix_commande) {
                            return response()->json([
                                'message' => 'Le prix doit être inférieur au prix précédent',
                            ]);
                        }

                        $commentaire_commande = 'Changement de prix (' . $request->commentaire_commande . 'Dhs) par ' . $user->username;
                        $commande->etat_commande = $request->statut;
                    } else if ($request->statut == 'RELANCER') {

                        $commentaire_commande = 'Relaunch request by ' . $user->username;
                        $commande->etat_commande = $request->statut;
                    }
                    $statut2 = HistoriqueCommande::create([
                        "id_commande" => $commande->id_commande,
                        "etat_commande" => $request->statut,
                        "commentaire_commande" => $commentaire_commande,
                        "reported_date" =>  $reported_date,
                        "id_client" => $user->id,
                    ]);

                    if ($statut2 && $request->statut == 'RELANCER') {
                        $statut2 = Notification::create([
                            "id_commande" => $commande->id_commande,
                            "description" => 'Demande Relance: ' . $commande->id_commande,
                            "titre" => 'Demande Relance',
                            "affichage" =>  'notSeen',
                            "id_client" => $user->id,
                            "id_employe" => $commande->livre_par,

                        ]);
                    }
                    $commande->updated_at = Carbon::now();
                    $commande->save();
                    if ($statut2) {
                        return response()->json([
                            'message' => 'Successfully',
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'Erreur',
                    ]);
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }

    public function getCommandeSuivie(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
                $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                    ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                    ->groupBy('id_commande');
                $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                    ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                    ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                    ->where('commandes.etat_commande', 'DMSUIVIE')
                    ->where(function ($query) use ($user) {
                        $query->where('commandes.id_client', $user->id)
                            ->orwhere('commandes.id_client', $user->superviseur);
                    })
                    ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                        $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                    })
                    ->selectRaw('commandes.id_commande,commandes.nom_client_commande,commandes.type_commande,stores.nom_store,clients.company,	
                commandes.telephone_client_commande,commandes.prix_commande,commandes.etat_commande,commandes.updated_at,villes.nom_ville as ville_client_commande,historiquecommandes.commentaire_commande')
                    ->orderBy('commandes.updated_at', 'desc')
                    ->paginate($_GET['count_nbr']);
                return response()->json([
                    'data' => $commandes
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function getBonRetour(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                if ($request->selected_option == 'id_bon_retour_client' && $request->valeur_recherche != ''){
                    $commandes = DB::table('bonretourclients')
                    ->selectRaw('id_bon_retour_client,statut_bonRetourClient,nbrColis_bonRetourClient,updated_at')
                    ->where('id_client', $user->id)
                    ->where('id_bon_retour_client','LIKE', "%$request->valeur_recherche%")
                    ->paginate($_GET['count_nbr']);
                }else if($request->selected_option == 'id_commande' && $request->valeur_recherche != ''){

                    $commandes = DB::table('bonretourclients')
                    ->join('commandes','commandes.id_bon_retour_client','bonretourclients.id_bon_retour_client')
                    ->selectRaw('id_bon_retour_client,statut_bonRetourClient,nbrColis_bonRetourClient,updated_at')
                    ->where('id_client', $user->id)
                   
                    ->paginate($_GET['count_nbr']);


                }else{
                    $commandes = DB::table('bonretourclients')
                    ->selectRaw('id_bon_retour_client,statut_bonRetourClient,nbrColis_bonRetourClient,updated_at')
                    ->where('id_client', $user->id)
                    ->paginate($_GET['count_nbr']);
                }
              
                return response()->json([
                    'data' => $commandes
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function getBonRetourClient($id)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $commandes = DB::table('bonretourclients')
                    ->selectRaw('id_bon_retour_client,statut_bonRetourClient,nbrColis_bonRetourClient')
                    ->where('id_client', $user->id)->where('bonretourclients.id_bon_retour_client', $id)
                    ->first();
                $contents = Storage::disk('s3')->download('public/Bons/BonRetourClient/' . $commandes->id_bon_retour_client . '.pdf');
                return $contents;
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }



    public function receptionRetour($id)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $commande_client = Commande::where('commandes.id_commande', $id)->where('commandes.id_client', $user->id)->where('commandes.etat_commande', 'RETURNEDRR')->first();
                $commande_client->etat_commande = 'RETURNED';
                $statut = $commande_client->save();


                if ($statut) {
                    $statut = HistoriqueCommande::create([
                        "id_commande" =>   $commande_client->id_commande,
                        "etat_commande" => 'RETURNED',
                        "commentaire_commande" => 'Return received by ' . $user->username,
                        "id_client" => $user->id,
                    ]);
                    if ($statut) {
                        return response()->json([
                            'message' => 'Commande RETURNED Successfully'
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Erreur'
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'Erreur'
                    ]);
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
}
