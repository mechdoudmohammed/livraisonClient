<?php

namespace App\Http\Controllers\Client;

use App\Exports\DataClients;
use App\Http\Controllers\Controller;
use App\Models\blacklist;
use App\Models\Client;
use App\Models\Commande;
use App\Models\DetailsCommandes;
use App\Models\HistoriqueCommande;
use App\Models\HistoriqueRamassage;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Package;
use App\Models\Packreductions;
use App\Models\Reclamation;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ClientController extends Controller
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
     * Clause WHERE pour filtrer les commandes du client ou de son superviseur.
     */
    private function scopeClientOrSupervisor($query, $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('commandes.id_client', $user->id)
              ->orWhere('commandes.id_client', $user->superviseur);
        });
    }

    /**
     * Retourne l'id_client réel (superviseur si EmployeClient).
     */
    private function resolveClientId($user): int
    {
        return $user->role === 'EmployeClient' ? $user->superviseur : $user->id;
    }

    /**
     * Crée un historique de ramassage selon le rôle du client.
     */
    private function createHistoriqueRamassage($user): void
    {
        HistoriqueRamassage::create([
            'id_ville'          => $user->id_ville,
            'statut_ramassage'  => 'pas encour',
            'id_client'         => $this->resolveClientId($user),
        ]);
    }

    /**
     * Retourne une réponse JSON succès ou erreur.
     */
    private function jsonStatus(bool $success, string $successMsg = 'Successfully', string $errorMsg = 'Erreur')
    {
        return response()->json(['message' => $success ? $successMsg : $errorMsg]);
    }

    // -------------------------------------------------------------------------
    // Actions du contrôleur
    // -------------------------------------------------------------------------

    /**
     * Liste tous les clients (réservé au rôle responsable).
     */
    public function index()
    {
        try {
            $user = $this->authUser();

            if ($user->role !== 'responsable') {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $clients = DB::table('clients')
                ->join('villes', 'clients.id_ville', '=', 'villes.id')
                ->select('clients.*', 'villes.nom_ville as ville')
                ->get();

            return response()->json(['data' => $clients]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Mise à jour du mot de passe du client connecté.
     */
    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required|string',
            'new_password' => 'required|string',
        ]);

        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user, clientOnly: true)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json(['message' => 'old password incorrect']);
            }

            if ($request->old_password === $request->new_password) {
                return response()->json(['message' => "old password and new password can't be same"]);
            }

            $client           = Client::find($user->id);
            $client->password = Hash::make($request->new_password);
            $saved            = $client->save();

            return $this->jsonStatus($saved, 'password update successfully');
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Mise à jour du profil client ou employé client.
     */
    public function modifier(Request $request)
    {
        $user   = $this->authUser();
        $client = Client::find($user->id);

        if ($this->isAuthorized($user, clientOnly: true)) {
            $profileComplete = $client->prenom && $client->nom && $client->cin;

            $rules = [
                'adresse'    => 'required|string',
                'company'    => 'required|string',
                'website'    => 'nullable|string',
                'ribBank'    => 'required|string',
                'email'      => 'required|max:150|unique:clients,email,' . $user->id,
                'telephone'  => 'required|regex:/(0)[0-9]{9}$/',
            ];

            if (!$profileComplete) {
                $rules = array_merge($rules, [
                    'nom'             => 'required|string',
                    'prenom'          => 'required|string',
                    'cin'             => 'required|string',
                    'telephone_store' => 'nullable|regex:/(0)[0-9]{9}$/',
                ]);

                $this->validate($request, $rules);

                $client->nom     = $request->nom;
                $client->prenom  = $request->prenom;
                $client->cin     = $request->cin;
                $client->email   = $request->email;
                $client->ribBank = $request->ribBank;
                $client->id_bank = $request->id_bank;
            } else {
                $this->validate($request, $rules);
            }

            $client->notification_statut = $request->notification_statut;
            $client->adresse             = $request->adresse;
            $client->telephone           = $request->telephone;
            $client->telephone_store     = $request->telephone_store;
            $client->company             = $request->company;
            $client->website             = $request->website;
            $client->save();

            return response()->json(['message' => 'Client update successfully']);
        }

        if ($user->role === 'EmployeClient' && $user->statut === 'Active') {
            $this->validate($request, [
                'telephone' => 'required|regex:/(0)[0-9]{9}$/',
            ]);

            $client->telephone = $request->telephone;
            $client->save();

            return response()->json(['message' => 'Client update successfully']);
        }
    }

    /**
     * Affiche un client (admin : toutes ses commandes ; sinon : fiche client seule).
     */
    public function show($id)
    {
        try {
            $user = $this->authUser();

            if ($user->role === 'SuperAdmin' && $user->statut === 'Active') {
                $client = Client::join('commandes', 'commandes.id_client', '=', 'clients.id')
                    ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                    ->leftJoin('employes as ramasseurs', 'ramasseurs.id', '=', 'commandes.ramasser_par')
                    ->leftJoin('employes as livreurs', 'livreurs.id', '=', 'commandes.livre_par')
                    ->where('clients.id', $id)
                    ->selectRaw('
                        ramasseurs.id as id_ramasseur, livreurs.id as id_livreur,
                        commandes.*, villes.nom_ville,
                        livreurs.nom as livreur, ramasseurs.nom as ramasseur
                    ')
                    ->get();
            } else {
                $client = Client::find($id);
            }

            return response()->json(['data' => $client]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Inscription d'un nouveau client.
     */
    public function inscription(Request $request)
    {
        $this->validate($request, [
            'ville'    => 'required',
            'username' => 'required|string|unique:clients,username|max:55',
            'email'    => 'required|unique:clients,email|email|max:150',
            'telephone' => 'required|regex:/(0)[0-9]{9}$/',
            'password' => 'required|string|min:8',
        ]);

        try {
            $user = Client::create([
                'username'  => $request->username,
                'telephone' => $request->telephone,
                'email'     => $request->email,
                'id_ville'  => $request->ville['id'],
                'password'  => Hash::make($request->password),
                'statut'    => 'Active',
            ]);

            if (!$user) {
                return response()->json(['message' => 'Erreur']);
            }

            event(new Registered($request));
            $user->sendEmailVerificationNotification();

            return $user->createToken($request->device_name)->plainTextToken;
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Connexion d'un client.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'       => 'required',
            'password'    => 'required',
            'device_name' => 'required',
        ]);

        $client = Client::where('email', $request->email)
            ->orWhere('username', $request->email)
            ->first();

        if (!$client || !Hash::check($request->password, $client->password)) {
            throw ValidationException::withMessages([
                'email' => ["Les informations d'identification fournies sont incorrectes."],
            ]);
        }

        if ($client->statut === 'Inactive') {
            return response()->json(['data' => 'bloque']);
        }

        return response()->json([
            'data'     => $client->createToken($request->device_name)->plainTextToken,
            'language' => $client->language,
        ]);
    }

    /**
     * Déconnexion du client courant.
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['msg' => 'Logout Successfull']);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Supprime un client (Client ou EmployeClient actif).
     */
    public function destroy($id)
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            Client::findOrFail($id)->delete();

            return response()->json('Record deleted!');
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Liste des villes actives.
     */
    public function getVilles()
    {
        try {
            $villes = DB::table('villes')
                ->where('statut', 'Active')
                ->select('id', 'nom_ville as ville')
                ->get();

            return response()->json(['data' => $villes]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Commentaire d'une ville par son id.
     */
    public function getVilleCommentaire($id)
    {
        try {
            $villes = DB::table('villes')
                ->where('id', $id)
                ->select('commentaire_ville as commentaire')
                ->get();

            return response()->json(['data' => $villes]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Liste paginée des packages du client (hors CREATED / CONFIRMED).
     */
    public function showPackage()
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $packages = DB::table('commandes')
                ->join('clients', 'commandes.id_client', '=', 'clients.id')
                ->join('packages', 'packages.id_package', 'commandes.id_package')
                ->where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                ->whereNotNull('commandes.id_package')
                ->whereNotIn('commandes.etat_commande', ['CREATED', 'CONFIRMED'])
                ->groupBy('commandes.id_package')
                ->orderBy('packages.created_at', 'desc')
                ->selectRaw('
                    packages.statut_package, commandes.id_package,
                    count(commandes.id_package) as nombre_commande,
                    commandes.id_commande, packages.updated_at, commandes.type_commande
                ')
                ->paginate(request('count_nbr'));

            return response()->json(['data' => $packages]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Demande de pickup pour une sélection de commandes (ou toutes les CONFIRMED).
     */
    public function pickupSelectedCommande(Request $request)
    {
        try {
            $user       = $this->authUser();
            $id_package = 'RM' . strtoupper(Str::random(6)) . time();

            Package::create(['id_package' => $id_package]);

            if (!$this->isAuthorized($user)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            DB::beginTransaction();

            $statut        = 0;
            $statutStock   = 0;
            $commandes     = $request->all();

            if (count($commandes) > 0) {
                // Traitement de la sélection manuelle
                foreach ($commandes as $item) {
                    $isStock    = $item['type_commande'] === 'stock' && $item['etat_commande'] === 'CONFIRMED';
                    $isRamassage = $item['type_commande'] === 'ramassage' && $item['etat_commande'] === 'CONFIRMED';

                    if ($isStock) {
                        $statutStock = Commande::where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                            ->where('etat_commande', 'CONFIRMED')
                            ->where('id_commande', $item['id_commande'])
                            ->update(['etat_commande' => 'PROCESSING']);

                        HistoriqueCommande::create([
                            'id_commande'  => $item['id_commande'],
                            'etat_commande' => 'PROCESSING',
                            'id_client'    => $user->id,
                        ]);
                    } elseif ($isRamassage) {
                        $statut = Commande::where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                            ->where('etat_commande', 'CONFIRMED')
                            ->where('id_commande', $item['id_commande'])
                            ->update(['id_package' => $id_package, 'etat_commande' => 'PICKUP']);

                        HistoriqueCommande::create([
                            'id_commande'  => $item['id_commande'],
                            'etat_commande' => 'PICKUP',
                            'id_client'    => $user->id,
                        ]);
                    }
                }

                if ($statut == 0 && $statutStock == 0) {
                    return response()->json(['message' => 'Erreur']);
                }

                if ($statut == 1 || $statutStock == 1) {
                    $this->createHistoriqueRamassage($user);
                }
            } else {
                // Traitement de toutes les commandes CONFIRMED
                $commandesStock = Commande::where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                    ->where('etat_commande', 'CONFIRMED')
                    ->where('type_commande', 'stock')
                    ->get();

                if ($commandesStock->isNotEmpty()) {
                    Commande::where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                        ->where('etat_commande', 'CONFIRMED')
                        ->where('type_commande', 'stock')
                        ->update(['etat_commande' => 'PROCESSING']);

                    foreach ($commandesStock as $commande) {
                        HistoriqueCommande::create([
                            'id_commande'  => $commande->id_commande,
                            'etat_commande' => 'PROCESSING',
                            'id_client'    => $user->id,
                        ]);
                    }
                }

                $commandesRamassage = Commande::where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                    ->where('etat_commande', 'CONFIRMED')
                    ->where('type_commande', 'ramassage')
                    ->get();

                if ($commandesRamassage->isNotEmpty()) {
                    Commande::where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                        ->where('etat_commande', 'CONFIRMED')
                        ->where('type_commande', 'ramassage')
                        ->update(['id_package' => $id_package, 'etat_commande' => 'PICKUP']);

                    foreach ($commandesRamassage as $commande) {
                        HistoriqueCommande::create([
                            'id_commande'  => $commande->id_commande,
                            'etat_commande' => 'PICKUP',
                            'id_client'    => $user->id,
                        ]);
                    }

                    $this->createHistoriqueRamassage($user);
                }

                if ($commandesRamassage->isEmpty() && $commandesStock->isEmpty()) {
                    return response()->json(['message' => 'Erreur']);
                }
            }

            DB::commit();

            return response()->json(['message' => 'commande created successfully']);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Confirme une sélection de commandes CREATED (stock ou ramassage).
     */
    public function confirmeSelectedCommande(Request $request)
    {
        try {
            $user   = $this->authUser();
            $statut = false;

            foreach ($request->all() as $item) {
                $isCreated = $item['etat_commande'] === 'CREATED';

                if ($isCreated && $item['type_commande'] === 'stock') {
                    $commande = Commande::where('id_commande', $item['id_commande'])->first();
                    $commande->etat_commande = 'CONFIRMED';
                    $statut = $commande->save();

                    if ($statut) {
                        HistoriqueCommande::create([
                            'id_commande'  => $item['id_commande'],
                            'etat_commande' => 'CONFIRMED',
                            'id_client'    => $user->id,
                        ]);
                    }
                } elseif ($isCreated && $item['type_commande'] === 'ramassage') {
                    $statut = Commande::where(fn($q) => $this->scopeClientOrSupervisor($q, $user))
                        ->where('id_commande', $item['id_commande'])
                        ->where('etat_commande', 'CREATED')
                        ->update(['etat_commande' => 'CONFIRMED']);

                    if ($statut) {
                        HistoriqueCommande::create([
                            'id_commande'  => $item['id_commande'],
                            'etat_commande' => 'CONFIRMED',
                            'id_client'    => $user->id,
                        ]);
                    }
                }
            }

            return $this->jsonStatus((bool) $statut, 'Commande Confirmed successfully', 'No order confirmed');
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Télécharge les données du client en Excel.
     */
    public function getClientsData()
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user, clientOnly: true)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            return Excel::download(new DataClients($user->id), 'DataClients.xlsx');
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Envoie une demande d'activation du stock (si pas déjà envoyée).
     */
    public function demandeActiverStock()
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user, clientOnly: true)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $exists = Notification::where('titre', 'Demande Activation Stock')
                ->where('id_client', $user->id)
                ->exists();

            if ($exists) {
                return response()->json(['message' => 'Demande déja envoyé']);
            }

            $statut = Notification::create([
                'description' => 'Demande Activation Stock par: ' . $user->id,
                'titre'       => 'Demande Activation Stock',
                'affichage'   => 'notSeen',
                'id_client'   => $user->id,
            ]);

            return $this->jsonStatus((bool) $statut, 'Demande envoyer successfully');
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Vérifie si un numéro est en liste noire.
     */
    public function checkBlackList($telephone)
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user, clientOnly: true)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $inBlacklist = blacklist::where('telephone', $telephone)->exists();

            return response()->json([
                'message' => $inBlacklist
                    ? 'Le numéro entrer est en liste noir'
                    : "Le numéro n'est pas en liste noir",
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Liste des annonces actives pour les clients.
     */
    public function getAnnonces()
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user, clientOnly: true)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $annonces = DB::table('annonces')
                ->where('type_user', 'Client')
                ->where('statut', 'Active')
                ->orderBy('updated_at')
                ->get();

            return response()->json(['data' => $annonces]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Envoie un message dans une réclamation.
     */
    public function sendMessage(Request $request)
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user, clientOnly: true)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $reclamation = Reclamation::where('id_client', $user->id)
                ->where('id_reclamation', $request->id_reclamation)
                ->first();

            if (in_array($reclamation->statut_reclamation, ['Close', 'Done'])) {
                return response()->json(['message' => 'Your claim is ' . $reclamation->statut_reclamation]);
            }

            $statut = Message::create([
                'id_reclamation' => $request->id_reclamation,
                'id_client'      => $user->id,
                'message'        => $request->message,
            ]);

            $reclamation->statut_reclamation = 'Pending';
            $reclamation->save();

            return $this->jsonStatus((bool) $statut, 'Message envoyer successfully');
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Récupère les messages d'une réclamation.
     */
    public function getMessages($id)
    {
        try {
            $user = $this->authUser();

            if (!$this->isAuthorized($user, clientOnly: true)) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }

            $messages = Message::leftJoin('employes', 'messages.id_employe', 'employes.id')
                ->join('reclamations', 'reclamations.id_reclamation', 'messages.id_reclamation')
                ->where('reclamations.id_client', $user->id)
                ->where('messages.id_reclamation', $id)
                ->orderBy('messages.created_at', 'asc')
                ->selectRaw('
                    messages.message, messages.created_at, messages.id_admin,
                    employes.nom, employes.prenom, messages.id_employe, messages.id_client
                ')
                ->get();

            return response()->json(['data' => $messages]);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Erreur']);
        }
    }

    /**
     * Envoi du lien de réinitialisation de mot de passe.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('clients')->sendResetLink($request->only('email'), null, 'client');

        if ($status === Password::RESET_LINK_SENT) {
            return ['status' => __($status)];
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    /**
     * Réinitialisation du mot de passe via token.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Client $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])
                     ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response(['message' => 'Password reset successfully']);
        }

        return response(['message' => __($status)], 500);
    }

    /**
     * Retourne le pack de réduction du client.
     */
    public function getMyPack()
    {
        $user = $this->authUser();

        if (!$this->isAuthorized($user, clientOnly: true)) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $pack = Packreductions::join('clients', 'clients.id_pack', 'packreductions.id')
            ->where('clients.id', $user->id)
            ->select('pack_name')
            ->first();

        return response()->json(['data' => $pack]);
    }
}
