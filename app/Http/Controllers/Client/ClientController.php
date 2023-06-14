<?php

namespace App\Http\Controllers\Client;

use App\Exports\DataClients;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use App\Models\Article;
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
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Verified;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'responsable') {
                $client = DB::table('clients')->join('villes', 'clients.id_ville', '=', 'villes.id')->select('clients.*', 'villes.nom_ville as ville')->get();
                return response()->json([
                    'data' => $client,
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required|String',
            'new_password' => 'required|String',

        ]);
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {


                if (!Hash::check($request->old_password, $user->password)) {
                    return response()->json([
                        'message' => 'old password incorrect'
                    ]);
                }
                if (strcmp($request->new_password, $request->old_password) == 0) {
                    return response()->json([
                        'message' => 'old password and new password can\'t be same'
                    ]);
                }
                $user = Client::where('id', $user->id)->first();
                if (Hash::check($request->old_password, $user->password)) {
                    $user->password = Hash::make($request->new_password);
                    $statut = $user->save();
                    if ($statut) {
                        return response()->json([
                            'message' => 'password update successfully'
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

    public function modifier(Request $request)
    {

        $user = auth('sanctum')->user();
        if ($user->role == 'Client' && $user->statut == 'Active') {
            $client = Client::find($user->id);
            if ($client->prenom != null && $client->nom != null && $client->cin != null) {
                $this->validate($request, [
                    'adresse' => 'required|String',
                    'company' => 'required|String',
                    'website' => 'nullable|String',
                    'ribBank' => 'required|String',
                    'email' => 'required|max:150|unique:clients,email,' . $user->id,
                    'telephone' => 'required|regex:/(0)[0-9]{9}$/',
                ]);
            } else {
                $this->validate($request, [
                    'nom' => 'required|String',
                    'prenom' => 'required|String',
                    'adresse' => 'required|String',
                    'cin' => 'required|String',
                    'company' => 'required|String',
                    'website' => 'nullable|String',
                    'ribBank' => 'required|String',
                    'email' => 'required|max:150|unique:clients,email,' . $user->id,
                    'telephone' => 'required|regex:/(0)[0-9]{9}$/',
                ]);
                $client->nom = $request->nom;
                $client->prenom = $request->prenom;
                $client->cin = $request->cin;
                $client->email = $request->email;
                $client->ribBank = $request->ribBank;
                $client->id_bank = $request->id_bank;
            }
            $client->notification_statut = $request->notification_statut;
            $client->adresse = $request->adresse;
            $client->telephone = $request->telephone;
            $client->company = $request->company;
            $client->website = $request->website;

            $client->save();
            return response()->json([
                'message' => 'Client update successfully'
            ]);
        }
        if ($user->role == 'EmployeClient' && $user->statut == 'Active') {
            $this->validate($request, [
                'telephone' => 'required|regex:/(0)[0-9]{9}$/',
            ]);
            $client = Client::find($user->id);
            $client->telephone = $request->telephone;
            $client->save();
            return response()->json([
                'message' => 'Client update successfully'
            ]);
        }
    }
    public function show($id)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'SuperAdmin' && $user->statut == 'Active') {
                $client = Client::join('commandes', 'commandes.id_client', '=', 'clients.id')
                    ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                    ->leftjoin('employes as ramasseurs', 'ramasseurs.id', '=', 'commandes.ramasser_par')
                    ->leftjoin('employes as livreurs', 'livreurs.id', '=', 'commandes.livre_par')
                    ->where('clients.id', $id)
                    ->selectRaw('ramasseurs.id as id_ramasseur,livreurs.id as id_livreur,commandes.*,villes.nom_ville,livreurs.nom as livreur,ramasseurs.nom as ramasseur')
                    ->get();
                return response()->json([
                    'data' => $client,
                ]);
            } else {
                $client = Client::find($id);
                return response()->json([
                    'data' => $client,

                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function inscription(Request $request)
    {
        $this->validate($request, [
            'ville' => 'required',
            'username' => 'required|String|unique:clients,username|max:55',
            'email' => 'required|unique:clients,email|email|max:150',
            'telephone' => 'required|regex:/(0)[0-9]{9}$/',
            'password' => 'required|String|min:8',
        ]);
        try {
            $user = Client::create([
                'username' => $request->username,
                'telephone' => $request->telephone,
                'email' => $request->email,
                'id_ville' => $request->ville['id'],
                'password' => Hash::make($request->password),
                'statut' => 'Active',
            ]);
            //}
            if ($user) {
                event(new Registered($request));
                $user->sendEmailVerificationNotification();
                return $user->createToken($request->device_name)->plainTextToken;
            } else {
                return response()->json([
                    'message' => 'Erreur'
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $client = Client::where('email', $request->email)->orWhere('username', $request->email)->first();

        if (!$client || !Hash::check($request->password, $client->password)) {

            throw ValidationException::withMessages([
                'email' => ['Les informations d\'identification fournies sont incorrectes.'],
            ]);
        }
        if ($client->statut == 'Inactive') {
            return response()->json(
                [
                    "data" => "bloque"
                ]

            );
        }
        return response()->json(
            [
                "data" => $client->createToken($request->device_name)->plainTextToken,
                'language' => $client->language
            ]

        );
    }
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['msg' => 'Logout Successfull']);
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
            if (($user->role == 'Client' || $user->role == 'EmployeClient') && $user->statut == 'Active') {
                $client = Client::find($id);
                $client->delete();
                return response()->json("Record deleted!");
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function getVilles()
    {
        try {
            $villes = DB::table('villes')->select('villes.id', 'villes.nom_ville as ville')->where('villes.statut', 'Active')->get();
            return response()->json([
                'data' => $villes
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function getVilleCommentaire($id)
    {
        try {
            $villes = DB::table('villes')->select('commentaire_ville as commentaire')->where('villes.id', $id)->get();
            return response()->json([
                'data' => $villes
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function showPackage()
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient') && $user->statut == 'Active') {
                $packagesClient = DB::table('commandes')
                    ->join('clients', 'commandes.id_client', '=', 'clients.id')
                    ->join('packages', 'packages.id_package', 'commandes.id_package')
                    ->where(function ($query) use ($user) {
                        $query->where('commandes.id_client', $user->id)
                            ->orwhere('commandes.id_client', $user->superviseur);
                    })
                    ->where('commandes.id_package', '!=', null)
                    ->whereNotIn('commandes.etat_commande', ['CREATED', 'CONFIRMED'])
                    ->groupBy('commandes.id_package')
                    ->orderBy('commandes.created_at', 'desc')
                    ->selectRaw('packages.statut_package,commandes.id_package , count(commandes.id_package) as nombre_commande,commandes.id_commande,packages.updated_at,commandes.type_commande')
                    ->paginate($_GET['count_nbr']);
                return response()->json([
                    'data' => $packagesClient
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }


    public function pickupSelectedCommande(Request $request)
    {
        try {
            $id_package = 'RM' . strtoupper(Str::random(6)) . time();
            Package::create(["id_package" => $id_package]);
            $user = auth('sanctum')->user();
            $statut2 = 0;
            $statut = 0;
            if (($user->role == 'Client' || $user->role == 'EmployeClient') && $user->statut == 'Active') {
                DB::beginTransaction();
                if (count($request->all()) > 0) {
                    for ($i = 0; $i < count($request->all()); $i++) {
                        if ($request[$i]['type_commande'] == 'stock' && $request[$i]['etat_commande'] == 'CONFIRMED') {
                            $statut2 = Commande::where(function ($query) use ($user) {
                                $query->where('commandes.id_client', $user->id)
                                    ->orwhere('commandes.id_client', $user->superviseur);
                            })
                                ->where('commandes.etat_commande', 'CONFIRMED')
                                ->where('commandes.id_commande', $request[$i]['id_commande'])
                                ->update(['etat_commande' => 'PROCESSING']);
                            HistoriqueCommande::create([
                                "id_commande" =>  $request[$i]['id_commande'],
                                "etat_commande" => 'PROCESSING',
                                "id_client" => $user->id,
                            ]);
                        } else if ($request[$i]['type_commande'] == 'ramassage' && $request[$i]['etat_commande'] == 'CONFIRMED') {
                            $statut = Commande::where(function ($query) use ($user) {
                                $query->where('commandes.id_client', $user->id)
                                    ->orwhere('commandes.id_client', $user->superviseur);
                            })
                                ->where('commandes.etat_commande', 'CONFIRMED')
                                ->where('commandes.id_commande', $request[$i]['id_commande'])
                                ->update(['id_package' => $id_package, 'etat_commande' => 'PICKUP']);
                            HistoriqueCommande::create([
                                "id_commande" =>  $request[$i]['id_commande'],
                                "etat_commande" => 'PICKUP',
                                "id_client" => $user->id,

                            ]);
                        }
                    }
                    if ($statut == 1 || $statut2 == 1) {
                        if ($user->role == 'Client') {
                            HistoriqueRamassage::create([
                                "id_ville" => $user->id_ville,
                                "statut_ramassage" => 'pas encour',
                                "id_client" => $user->id,
                            ]);
                        } elseif ($user->role == 'EmployeClient') {
                            HistoriqueRamassage::create([
                                "id_ville" => $user->id_ville,
                                "statut_ramassage" => 'pas encour',
                                "id_client" => $user->superviseur,
                            ]);
                        }
                    } elseif ($statut == 0 && $statut2 == 0) {
                        return response()->json([
                            'message' => 'Erreur'
                        ]);
                    }
                } else {
                    $commandesStock = Commande::where(function ($query) use ($user) {
                        $query->where('commandes.id_client', $user->id)
                            ->orwhere('commandes.id_client', $user->superviseur);
                    })
                        ->where('commandes.etat_commande', 'CONFIRMED')
                        ->where('commandes.type_commande', 'stock')
                        ->get();
                    Commande::where(function ($query) use ($user) {
                        $query->where('commandes.id_client', $user->id)
                            ->orwhere('commandes.id_client', $user->superviseur);
                    })
                        ->where('commandes.etat_commande', 'CONFIRMED')
                        ->where('commandes.type_commande', 'stock')
                        ->update(['etat_commande' => 'PROCESSING']);

                    foreach ($commandesStock as $commande) {
                        HistoriqueCommande::create([
                            "id_commande" =>  $commande->id_commande,
                            "etat_commande" => 'PROCESSING',
                            "id_client" => $user->id,
                        ]);
                    }

                    $commandesRamassage = Commande::where(function ($query) use ($user) {
                        $query->where('commandes.id_client', $user->id)
                            ->orwhere('commandes.id_client', $user->superviseur);
                    })
                        ->where('commandes.etat_commande', 'CONFIRMED')
                        ->where('commandes.type_commande', 'ramassage')
                        ->get();

                    Commande::where(function ($query) use ($user) {
                        $query->where('commandes.id_client', $user->id)
                            ->orwhere('commandes.id_client', $user->superviseur);
                    })
                        ->where('commandes.etat_commande', 'CONFIRMED')
                        ->where('commandes.type_commande', 'ramassage')
                        ->update(['id_package' => $id_package, 'etat_commande' => 'PICKUP']);

                    foreach ($commandesRamassage as $commande) {
                        HistoriqueCommande::create([
                            "id_commande" =>  $commande->id_commande,
                            "etat_commande" => 'PICKUP',
                            "id_client" => $user->id,
                        ]);
                    }
                    if (count($commandesRamassage) > 0) {
                        if ($user->role == 'Client') {
                            HistoriqueRamassage::create([
                                "id_ville" => $user->id_ville,
                                "statut_ramassage" => 'pas encour',
                                "id_client" => $user->id,
                            ]);
                        } elseif ($user->role == 'EmployeClient') {
                            HistoriqueRamassage::create([
                                "id_ville" => $user->id_ville,
                                "statut_ramassage" => 'pas encour',
                                "id_client" => $user->superviseur,
                            ]);
                        }
                    }

                    if (count($commandesRamassage) == 0 && count($commandesStock) == 0) {
                        return response()->json([
                            'message' => 'Erreur'
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json([

                'message' => 'commande created successfully'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function confirmeSelectedCommande(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $statut = false;
            for ($i = 0; $i < count($request->all()); $i++) {
                if ($request[$i]['etat_commande'] == 'CREATED' && $request[$i]['type_commande'] == 'stock') {
                    $commande_client = DetailsCommandes::join('commandes', 'commandes.id_commande', 'detailscommandes.id_commande')
                        ->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->where('commandes.id_client', $user->id)
                        ->where('commandes.id_commande', $request[$i]['id_commande'])
                        ->where('commandes.etat_commande', 'CREATED')
                        ->selectRaw('commandes.id_commande,id_article,quantite_article,type_commande')
                        ->get();
                    //pour savoir le nombre des articles deja confirmer
                    // for ($n = 0; $n < count($commande_client); $n++) {
                    //     $article = Commande::whereIn('commandes.etat_commande', ['CONFIRMED', 'PROCESSING', 'PICKUP', 'INHOUSE'])
                    //         ->join('detailscommandes', 'detailscommandes.id_commande', 'commandes.id_commande')
                    //         ->where('commandes.id_client', $user->id)
                    //         ->where('detailscommandes.id_article', $commande_client[$n]->id_article)
                    //         ->selectRaw('sum(detailscommandes.quantite_article) as qnt_article')
                    //         ->first();
                    //     // that if means it's the first confirmed of the article
                    //     if ($article->qnt_article == null) {
                    //         $article_in_stock = Article::where('articles.id_article', $commande_client[$n]->id_article)
                    //             ->selectRaw('articles.stock_article as qnt_article_stock')
                    //             ->first();
                    //         $articles = $article_in_stock->qnt_article_stock;
                    //     } else {
                    //         //pour savoir la quantite exist en stock
                    //         $article_in_stock = Article::where('articles.id_article', $commande_client[$n]->id_article)
                    //             ->selectRaw('articles.stock_article as qnt_article_stock')
                    //             ->first();
                    //         $articles = $article_in_stock->qnt_article_stock - $article->qnt_article;
                    //     }
                    //     if ($commande_client[$n]->quantite_article > (int)$articles) {
                    //         return response()->json([
                    //             'message' => 'Quantity not available in:' . $commande_client[$n]->id_commande
                    //         ]);
                    //     }
                    // }

                    $commande = Commande::where('commandes.id_commande', $request[$i]['id_commande'])->first();
                    $commande->etat_commande = 'CONFIRMED';
                    $statut = $commande->save();
                    if ($statut) {
                        HistoriqueCommande::create([
                            "id_commande" =>  $request[$i]['id_commande'],
                            "etat_commande" => 'CONFIRMED',
                            "id_client" => $user->id,
                        ]);
                    }
                } elseif ($request[$i]['etat_commande'] == 'CREATED' && $request[$i]['type_commande'] == 'ramassage') {

                    $commande_client = Commande::join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->where('commandes.id_commande', $request[$i]['id_commande'])
                        ->where('commandes.etat_commande', 'CREATED')
                        ->selectRaw('commandes.id_commande')
                        ->first();

                    $statut = Commande::where('commandes.id_commande', $request[$i]['id_commande'])
                        ->update(['etat_commande' => 'CONFIRMED']);
                    if ($statut) {
                        HistoriqueCommande::create([
                            "id_commande" =>  $request[$i]['id_commande'],
                            "etat_commande" => 'CONFIRMED',
                            "id_client" => $user->id,
                        ]);
                    }
                }
            }
            if ($statut) {
                return response()->json([
                    'message' => 'Commande Confirmed successfully'
                ]);
            } else {
                return response()->json([
                    'message' => 'No order confirmed'
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function getClientsData()
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                return Excel::download(new DataClients($user->id), 'DataClients.xlsx');
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function demandeActiverStock()
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $notification = Notification::where('titre', 'Demande Activation Stock')->where('id_client', $user->id)->first();
                if ($notification) {
                    return response()->json([
                        'message' => 'Demande déja envoyé'
                    ]);
                } else {
                    $statut = Notification::create([
                        "description" => 'Demande Activation Stock par: ' . $user->id,
                        "titre" => 'Demande Activation Stock',
                        "affichage" =>  'notSeen',
                        "id_client" => $user->id,

                    ]);
                }


                if ($statut) {
                    return response()->json([
                        'message' => 'Demande envoyer successfully'
                    ]);
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
    public function checkBlackList($telephone)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $blacklists = blacklist::where('telephone', $telephone)
                    ->selectRaw('blacklists.telephone')
                    ->first();

                if ($blacklists != '') {
                    return response()->json([
                        'message' => 'Le numéro entrer est en liste noir'
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Le numéro n\'est pas en liste noir'
                    ]);
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function getAnnonces()
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $annonces = DB::table('annonces')->where('type_user', 'Client')->where('statut', 'Active')->orderBy('updated_at')->get();
                return response()->json([
                    'data' => $annonces,
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function sendMessage(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $reclamation = Reclamation::where('id_client', $user->id)->where('id_reclamation', $request->id_reclamation)->first();

                if ($reclamation->statut_reclamation == "Close" || $reclamation->statut_reclamation == "Done") {
                    return response()->json([
                        'message' => 'Your claim is ' . $reclamation->statut_reclamation
                    ]);
                } else {
                    $statut = Message::create([
                        "id_reclamation" =>  $request->id_reclamation,
                        "id_client" => $user->id,
                        "message" => $request->message,
                    ]);

                    $reclamation->statut_reclamation = 'Pending';
                    $reclamation->save();

                    if ($statut) {
                        return response()->json([
                            'message' => 'Message envoyer successfully'
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
    public function getMessages($id)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $messages = Message::leftjoin('employes', 'messages.id_employe', 'employes.id')
                    ->join('reclamations', 'reclamations.id_reclamation', 'messages.id_reclamation')
                    ->where('reclamations.id_client', $user->id)

                    ->where('messages.id_reclamation', $id)

                    ->orderBy('messages.created_at', 'asc')
                    ->selectRaw('messages.message,messages.created_at,messages.id_admin,employes.nom,employes.prenom,messages.id_employe,messages.id_client')
                    ->get();

                if ($messages) {
                    return response()->json([
                        'data' => $messages
                    ]);
                } else {
                    return response()->json([
                        'data' => 'Erreur'
                    ]);
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::broker('clients')->sendResetLink($request->only('email'), null, 'client');


        if ($status == Password::RESET_LINK_SENT) {
            return [
                'status' => __($status)
            ];
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    public function reset(Request $request)
    {

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Client $user, string $password) {

                $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response([
                'message' => 'Password reset successfully'
            ]);
        }

        return response([
            'message' => __($status)
        ], 500);
    }
    public function getMyPack()
    {

        $user = auth('sanctum')->user();
        if ($user->role == 'Client' && $user->statut == 'Active') {
            $Packreductions = Packreductions::join('clients', 'clients.id_pack', 'packreductions.id')->select('pack_name')->where('clients.id', $user->id)->first();
            return response()->json([
                'data' => $Packreductions
            ]);
        }
    }
}
