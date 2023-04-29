<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\Models\HistoriqueCommande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class RechercheController extends Controller
{
    public function rechercheCommande(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $list = ['CREATED', 'CONFIRMED', 'PICKUP', 'PROCESSING', 'ENROUTE', 'TRANSIT', 'REPORTED', 'DELIVERED', 'RAMASSER'];
            if (($user->role == 'Client' || $user->role == 'EmployeClient') && $user->statut == 'Active') {
                if (in_array($request->selected_option2, $list) && $request->selected_option == 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('commandes.etat_commande', $request->selected_option2)
                        ->Where('villes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->selected_option2 == 'INHOUSE' && $request->selected_option == 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('villes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->where(function ($query) use ($user) {
                            $query->Where('commandes.etat_commande', 'INHOUSE')
                                ->OrWhere('commandes.etat_commande', 'HOME');
                        })
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->selected_option2 == 'RETURNED' && $request->selected_option == 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('villes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->whereIn('commandes.etat_commande', ['RETURNEDLV', 'RETURNED', 'RETURNEDEV', 'RETURNEDRR', 'RETURNEDAG'])
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                }
                else if ($request->selected_option2 == 'CANCELED' && $request->selected_option == 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('villes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->whereIn('commandes.etat_commande', ['CANCELEDLV', 'CANCELED', 'CANCELEDEV', 'CANCELEDRR', 'CANCELEDAG'])
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                }
                else if ($request->selected_option2 == 'ANNULER' && $request->selected_option == 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('villes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->whereIn('commandes.etat_commande', ['ANNULER', 'ANNULER_CL'])
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                }
                 else if ($request->selected_option2 == 'NOREPONSE' && $request->selected_option == 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('villes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->Where('commandes.etat_commande', 'NOREPONSE')
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->selected_option2 == 'DEFAULT' && $request->selected_option == 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('villes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->valeur_recherche == '' && $request->selected_option2 != '' && $request->selected_option == '') {

                    return $this->classifierCommande($request);
                } else if ($request->valeur_recherche == '' && $request->selected_option2 == 'DEFAULT' && $request->selected_option != '') {

                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                }
                if (in_array($request->selected_option2, $list) && $request->selected_option != '' && $request->selected_option != 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('commandes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->Where('commandes.etat_commande', $request->selected_option2)
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->selected_option2 == 'INHOUSE' && $request->selected_option != '' && $request->selected_option != 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('commandes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->where(function ($query) use ($user) {
                            $query->Where('commandes.etat_commande', 'INHOUSE')
                                ->OrWhere('commandes.etat_commande', 'HOME');
                        })
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->selected_option2 == 'RETURNED' && $request->selected_option != '' && $request->selected_option != 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('commandes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->whereIn('commandes.etat_commande', ['RETURNEDLV', 'RETURNED', 'RETURNEDEV', 'RETURNEDRR', 'RETURNEDAG'])
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                }else if ($request->selected_option2 == 'CANCELED' && $request->selected_option != '' && $request->selected_option != 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('commandes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->whereIn('commandes.etat_commande', ['CANCELEDLV', 'CANCELED', 'CANCELEDEV', 'CANCELEDRR', 'CANCELEDAG'])
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                }
                else if ($request->selected_option2 == 'ANNULER' && $request->selected_option != '' && $request->selected_option != 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('commandes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->whereIn('commandes.etat_commande', ['ANNULER', 'ANNULER_CL'])
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->selected_option2 == 'NOREPONSE' && $request->selected_option != '' && $request->selected_option != 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('commandes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->Where('commandes.etat_commande', 'NOREPONSE')
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->selected_option2 == 'DEFAULT' && $request->selected_option != '' && $request->selected_option != 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('commandes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->selected_option2 != '' && $request->selected_option != '' && $request->valeur_recherche == '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function rechercheCommandeSuivie(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient') && $user->statut == 'Active') {
                if ($request->selected_option == 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('commandes.etat_commande', 'DMSUIVIE')
                        ->Where('villes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                }
                if ($request->selected_option != 'nom_ville' && $request->valeur_recherche != '') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('commandes.' . $request->selected_option, 'LIKE', "%{$request->valeur_recherche}%")
                        ->Where('commandes.etat_commande', 'DMSUIVIE')
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('commandes.*', 'clients.company', 'stores.nom_store', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function classifierCommande(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $list = ['CREATED', 'CONFIRMED', 'PICKUP', 'PROCESSING', 'ENROUTE', 'TRANSIT', 'REPORTED', 'DELIVERED', 'RAMASSER'];
            if (($user->role == 'Client' || $user->role == 'EmployeClient') && $user->statut == 'Active') {
                if (in_array($request->selected_option2, $list)) {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('commandes.etat_commande', $request->selected_option2)
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'stores.nom_store', 'clients.company', 'commandes.*', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->selected_option2 == 'INHOUSE') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->Where('commandes.etat_commande', 'INHOUSE')
                                ->OrWhere('commandes.etat_commande', 'HOME');
                        })
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'stores.nom_store', 'clients.company', 'commandes.*', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->selected_option2 == 'RETURNED') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->whereIn('commandes.etat_commande', ['RETURNEDLV', 'RETURNED', 'RETURNEDEV', 'RETURNEDRR', 'RETURNEDAG'])
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'stores.nom_store', 'clients.company', 'commandes.*', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                }
                else if ($request->selected_option2 == 'CANCELED') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->whereIn('commandes.etat_commande', ['CANCELEDLV', 'CANCELED', 'CANCELEDEV', 'CANCELEDRR', 'CANCELEDAG'])
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'stores.nom_store', 'clients.company', 'commandes.*', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                }
                else if ($request->selected_option2 == 'ANNULER') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->whereIn('commandes.etat_commande', ['ANNULER', 'ANNULER_CL'])
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'stores.nom_store', 'clients.company', 'commandes.*', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->selected_option2 == 'NOREPONSE') {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->Where('commandes.etat_commande', 'NOREPONSE')
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'stores.nom_store', 'clients.company', 'commandes.*', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
                    ]);
                } else if ($request->selected_option2 == 'DEFAULT') {

                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->selectRaw('factures.statut_facture,factures.type_facture,commandes.etat_commande,stores.nom_store,clients.company,commandes.*,villes.nom_ville as ville_client_commande,historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);

                    return response()->json([
                        'data' => $commandes
                    ]);
                } else {
                    $sub = HistoriqueCommande::orderBy('updated_at', 'desc');
                    $historiquecommandes = DB::table(DB::raw("({$sub->toSql()}) as historiquecommandes"))
                        ->whereIn('historiquecommandes.commentaire_commande', ["Pas de réponse", "Retours envoye vers agence"])
                        ->groupBy('id_commande');
                    $commandes = DB::table('commandes')->join('clients', 'commandes.id_client', '=', 'clients.id')
                        ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                        ->leftjoin('stores', 'stores.id', '=', 'commandes.id_store')
                        ->leftjoin('factures', 'commandes.id_facture', 'factures.id_facture')
                        ->where(function ($query) use ($user) {
                            $query->where('commandes.id_client', $user->id)
                                ->orwhere('commandes.id_client', $user->superviseur);
                        })
                        ->leftjoinSub($historiquecommandes, 'historiquecommandes', function ($join) {
                            $join->on('commandes.id_commande', '=', 'historiquecommandes.id_commande');
                        })
                        ->select('factures.statut_facture', 'factures.type_facture', 'stores.nom_store', 'clients.company', 'commandes.*', 'villes.nom_ville as ville_client_commande', 'historiquecommandes.commentaire_commande')
                        ->orderBy('commandes.updated_at', 'desc')
                        ->paginate($_GET['count_nbr']);
                    return response()->json([
                        'data' => $commandes
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
