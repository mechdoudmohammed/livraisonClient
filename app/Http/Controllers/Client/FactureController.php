<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\Models\Commande;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                if ($request->selected_option == "id_facture" && $request->valeur_recherche != '') {

                        $clients = DB::table('factures')
                        ->leftjoin('commandes', 'commandes.id_facture', 'factures.id_facture')
                        ->leftjoin('manualfactures', 'manualfactures.id_facture', 'factures.id_facture')
                        ->whereIn('factures.statut_facture', ['NOTPAID', 'PAID'])
                        ->where('factures.id_client', $user->id)
                        ->where('factures.id_facture', 'LIKE', "%{$request->valeur_recherche}%")
                        ->groupBy('factures.id_facture')
                        ->orderBy('factures.updated_at', 'desc')
                        ->selectRaw('factures.updated_at,factures.type_facture,factures.total_facture,factures.frais_livraison_facture,count(factures.id_facture) as nombre_commande,factures.statut_facture,factures.id_facture')
                        ->paginate($_GET['count_nbr']);
                }
                else if ($request->selected_option == "id_commande" && $request->valeur_recherche != '') {

                    $clients = DB::table('factures')
                    ->leftjoin('commandes', 'commandes.id_facture', 'factures.id_facture')
                    ->leftjoin('manualfactures', 'manualfactures.id_facture', 'factures.id_facture')
                    ->whereIn('factures.statut_facture', ['NOTPAID', 'PAID'])
                    ->where('factures.id_client', $user->id)
                    ->where('commandes.id_commande', 'LIKE', "%{$request->valeur_recherche}%")
                    ->groupBy('factures.id_facture')
                    ->orderBy('factures.updated_at', 'desc')
                    ->selectRaw('factures.updated_at,factures.type_facture,factures.total_facture,factures.frais_livraison_facture,count(factures.id_facture) as nombre_commande,factures.statut_facture,factures.id_facture')
                    ->paginate($_GET['count_nbr']);
            }
                else {
                    $clients = DB::table('factures')
                        ->leftjoin('commandes', 'commandes.id_facture', 'factures.id_facture')
                        ->leftjoin('manualfactures', 'manualfactures.id_facture', 'factures.id_facture')

                        ->whereIn('factures.statut_facture', ['NOTPAID', 'PAID'])
                        ->where('factures.id_client', $user->id)
                        ->groupBy('factures.id_facture')
                        ->orderBy('factures.updated_at', 'desc')
                        ->selectRaw('factures.updated_at,factures.type_facture,factures.total_facture,factures.frais_livraison_facture,count(factures.id_facture) as nombre_commande,factures.statut_facture,factures.id_facture')
                        ->paginate($_GET['count_nbr']);
                }


                return response()->json([
                    'data' => $clients,
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }

    // public function show($id)
    // {
    //     $user = auth('sanctum')->user();
    //     if ($user->role == 'Client' && $user->statut == 'Active') {

    //         $commandeSelected = DB::table('commandes')
    //             ->join('factures', 'factures.id_facture', '=', 'commandes.id_facture')
    //             ->where('commandes.id_commande', $id)
    //             ->where('commandes.id_client', $user->id)
    //             ->select('commandes.id_facture','commandes.id_commande')
    //             ->first();

    //         if ($commandeSelected) {
    //             $commandes = DB::table('commandes')
    //             ->join('factures', 'factures.id_facture', '=', 'commandes.id_facture')
    //             ->join('villes', 'villes.id', 'commandes.id_ville')
    //             ->where('commandes.id_facture', $commandeSelected->id_facture)
    //             ->where('commandes.id_client', $user->id)
    //             ->orderBy('commandes.updated_at','desc')
    //             ->select('commandes.etat_commande', 'commandes.id_facture', 'commandes.prix_commande', 'commandes.id_commande', 'villes.nom_ville', 'villes.prix_retour', 'villes.prix_refus', 'commandes.prix_livraison_final')
    //             ->get();
    //         $resultat = [
    //             'delivered' => 0,
    //             'returned' => 0,
    //             'canceled' => 0,
    //             'total_delivered' => 0,
    //             'total_returned' => 0,
    //             'total_canceled' => 0,
    //             'total_livraison' => 0,
    //         ];
    //             $facture = DB::table('factures')->join('commandes', 'factures.id_facture', '=', 'commandes.id_facture')
    //                 ->where('factures.id_facture', $commandeSelected->id_facture)->where('commandes.id_client', $user->id)
    //                 ->select('factures.id_facture', 'factures.date_facture')
    //                 ->first();


    //             foreach ($commandes as $commande) {
    //                 if ($commande->etat_commande == 'DELIVERED') {
    //                     $resultat['delivered'] += 1;
    //                     $resultat['total_delivered'] += $commande->prix_commande;
    //                     $resultat['total_livraison'] += $commande->prix_livraison_final;
    //                 } elseif ($commande->etat_commande == 'RETURNED') {
    //                     $resultat['returned'] += 1;
    //                     $resultat['total_returned'] += $commande->prix_retour;
    //                 } elseif ($commande->etat_commande == 'CANCELED') {
    //                     $resultat['canceled'] += 1;
    //                     $resultat['total_canceled'] += $commande->prix_refus;
    //                 }
    //             }
    //             $data = ['data' => $resultat, 'data2' => $commandes, 'data3' => $user, 'data4' => $facture,'commande'=>$commandeSelected->id_commande,];

    //             $pdf = PDF::loadView('facture', $data);
    //             return $pdf->stream('document.pdf');

    //         }


    //     }
    // }

    public function getFacture($id)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $facture = DB::table('factures')
                    ->where('factures.id_facture', $id)
                    ->where('factures.id_client', $user->id)
                    ->select('factures.id_facture', 'factures.type_facture')
                    ->first();

                if ($facture->type_facture == 'clientManual') {
                    $contents = Storage::disk('s3')->download('public/factures/facturesClientManual/' . $facture->id_facture . '.pdf');
                    return $contents;
                } else if ($facture->type_facture == 'client') {
                    $contents = Storage::disk('s3')->download('public/factures/facturesClient/' . $facture->id_facture . '.pdf');
                    return $contents;
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }



    public function bonRamassage(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient') && $user->statut == 'Active') {
                $packageClient = DB::table('commandes')
                    ->join('clients', 'commandes.id_client', '=', 'clients.id')
                    ->join('villes', 'commandes.id_ville', '=', 'villes.id')
                    ->join('villes as villes2', 'clients.id_ville', '=', 'villes2.id')
                    ->where(function ($query) use ($user) {
                        $query->where('commandes.id_client', $user->id)
                            ->orwhere('commandes.id_client', $user->superviseur);
                    })
                    ->where('commandes.id_package', $request->id)
                    ->select('commandes.id_commande', 'commandes.nom_client_commande', 'commandes.telephone_client_commande', 'commandes.prix_commande', 'villes.nom_ville as ville', 'clients.company', 'clients.telephone as telephone_client', 'villes2.nom_ville as ville_client')
                    ->orderBy('.commandes.updated_at', 'desc')
                    ->get();

                $data = ['data' => $user, 'data2' => $packageClient];
                // return $data;


                $pdf = PDF::loadView('bonRamassage', $data)
                    ->setOption('margin-top', 1)
                    ->setOption('margin-right', 1)
                    ->setOption('margin-left', 1)
                    ->setOption('margin-bottom', 1);
                return $pdf->stream('document.pdf');

                // return response()->json([
                //     'data' => $clients,
                // ]);

            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
}
