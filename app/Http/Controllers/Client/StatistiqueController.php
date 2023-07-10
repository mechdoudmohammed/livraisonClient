<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\Models\Commande;
use App\Models\Article;
use App\Models\Packreductions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class StatistiqueController extends Controller
{

    public function commandeStatistiquesRevenue(Request $request)
    {

        if (count($request->all()) > 1 && $request[0] != null && $request[1] != null) {
            $date_debut = Carbon::parse($request[0])->addDays(-1);
            $date_fin = Carbon::parse($request[1])->addDays(1);
        } else {
            $date_debut = Carbon::now()->firstOfMonth();
            $date_fin = Carbon::now()->lastOfMonth();
        }

        $user = auth('sanctum')->user();
        $commandeRevenu = DB::table("commandes")
            ->whereIn('etat_commande', ['DELIVERED'])
            ->where('updated_at', '>', $date_debut)
            ->where('updated_at', '<', $date_fin)
            ->where('id_client', $user->id)


            ->groupBy(DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d')"))
            ->selectRaw("DATE_FORMAT(updated_at, '%Y-%m-%d') as date,sum(prix_commande) as somme")
            ->get();


        if (count($commandeRevenu) > 0) {
            for ($i = 0; $i < sizeof($commandeRevenu); $i++) {
                $res_array[$i] = $commandeRevenu[$i]->somme;
                $res_array2[$i] = $commandeRevenu[$i]->date;
            }
            return response()->json([
                'data' => $res_array,
                'data2' => $res_array2,
            ]);
        }
    }
    public function statistiquesVille(Request $request)
    {
        $user = auth('sanctum')->user();

        if (count($request->all()) > 1 && $request[0] != null && $request[1] != null) {
            $date_debut = Carbon::parse($request[0])->addDays(-1);
            $date_fin = Carbon::parse($request[1])->addDays(1);
        } else {
            $date_debut = Carbon::now()->firstOfMonth();
            $date_fin = Carbon::now()->lastOfMonth();
        }

        $ville = DB::table("commandes")
            ->join('villes', 'villes.id', 'commandes.id_ville')
            ->where('etat_commande', 'DELIVERED')
            ->where('commandes.updated_at', '>', $date_debut)
            ->where('commandes.updated_at', '<', $date_fin)
            ->where('commandes.id_client', $user->id)
            ->groupBy('commandes.id_ville')
            ->selectRaw("villes.nom_ville,count(commandes.id_commande) as somme")
            ->orderBy('somme', 'desc')
            ->take(10)
            ->get();

        if (count($ville) > 0) {

            for ($i = 0; $i < sizeof($ville); $i++) {
                $res_array[$i] =  $ville[$i]->somme;
                $res_array2[$i] =  $ville[$i]->nom_ville;
            }
            return response()->json([
                'data' => $res_array,
                'data2' => $res_array2,
            ]);
        }
    }


    public function getDeliveredCommande(Request $request)
    {
        if (count($request->all()) > 1 && $request[0] != null && $request[1] != null) {
            $date_debut = Carbon::parse($request[0]);
            $date_fin = Carbon::parse($request[1]);
        } else {
            $date_debut = Carbon::now()->firstOfMonth();
            $date_fin = Carbon::now()->lastOfMonth();
        }
        $user = auth('sanctum')->user();
        if ($user->role == 'Client') {
            $reduction = Packreductions::join('clients', 'clients.id_pack', 'packreductions.id')->where('clients.id', $user->id)
            ->selectRaw('packreductions.reduc_delivered,packreductions.reduc_canceled,packreductions.reduc_returned')
            ->first();
            $reduc_delivered = 0;
            $reduc_returned = 0;
            $reduc_canceled = 0;
            if ($reduction) {
                $reduc_delivered = $reduction->reduc_delivered;
                $reduc_returned = $reduction->reduc_returned;
                $reduc_canceled = $reduction->reduc_canceled;
            }
            $RevenuOfMonth = DB::table("historiquecommandes")
                ->join('commandes', 'commandes.id_commande', 'historiquecommandes.id_commande')
                ->where('historiquecommandes.updated_at', '>', $date_debut)
                ->where('historiquecommandes.updated_at', '<', $date_fin)
                ->whereIn('historiquecommandes.etat_commande', ['DELIVERED'])
                ->where('commandes.id_client', $user->id)
                ->selectRaw("coalesce(sum(commandes.prix_commande),0) as somme")
                ->first();

            $Livraison = DB::table("historiquecommandes")
            ->join('commandes', 'commandes.id_commande', 'historiquecommandes.id_commande')
                ->join('villes', 'villes.id', 'commandes.id_ville')
                ->whereIn('historiquecommandes.etat_commande', ['DELIVERED', 'CANCELEDLV', 'CANCELEDAG', 'CANCELEDEV', 'CANCELED', 'CANCELEDRR', 'RETURNEDLV', 'RETURNEDAG', 'RETURNEDEV', 'RETURNED', 'RETURNEDRR'])
                ->where('historiquecommandes.updated_at', '>', $date_debut)
                ->where('historiquecommandes.updated_at', '<', $date_fin)
                ->where('commandes.id_client', $user->id)
                ->selectRaw('commandes.etat_commande,villes.prix_retour,villes.prix_refus,commandes.prix_livraison_final')
                ->get();

                $colisNonFacture =DB::table("historiquecommandes") 
                ->join('commandes', 'commandes.id_commande', 'historiquecommandes.id_commande')
                ->where('commandes.id_client', $user->id)
                ->whereIn('historiquecommandes.etat_commande', ['DELIVERED'])
                ->where('commandes.paid', 0)
                ->where('historiquecommandes.updated_at', '>', $date_debut)
                ->where('historiquecommandes.updated_at', '<', $date_fin)
                ->selectRaw("count(historiquecommandes.id_commande) as nbrColis")
                ->first();

            $fraisLivraison = 0;
            foreach ($Livraison as $commande) {
                if ($commande->etat_commande == 'DELIVERED') {
                    $fraisLivraison += ($commande->prix_livraison_final - $reduc_delivered);
                } elseif ($commande->etat_commande == 'RETURNEDAG'  || $commande->etat_commande == 'RETURNEDEV' || $commande->etat_commande == 'RETURNEDRR' || $commande->etat_commande == 'RETURNED') {
                    $fraisLivraison += ($commande->prix_retour - $reduc_returned);
                } elseif ($commande->etat_commande == 'CANCELEDAG'  || $commande->etat_commande == 'CANCELEDEV' || $commande->etat_commande == 'CANCELEDRR' || $commande->etat_commande == 'CANCELED') {
                    $fraisLivraison +=($commande->prix_refus - $reduc_canceled);
                }
            }



                $deliverdCommande = DB::table("commandes")
                ->where('commandes.updated_at', '>', $date_debut)
                ->where('commandes.updated_at', '<', $date_fin)
                ->whereIn('commandes.etat_commande', ['DELIVERED'])
                ->where('commandes.id_client', $user->id)
                ->selectRaw("count(commandes.id_commande) as nbrColis")
                ->first();

                $returnedCommande = DB::table("commandes")
                ->where('commandes.updated_at', '>', $date_debut)
                ->where('commandes.updated_at', '<', $date_fin)
                ->whereIn('commandes.etat_commande', ['RETURNEDAG', 'RETURNEDLV', 'RETURNEDEV', 'RETURNEDRR', 'RETURNED'])
                ->where('commandes.id_client', $user->id)
                ->selectRaw("count(commandes.id_commande) as nbrColis")
                ->first();

                $canceledCommande = DB::table("commandes")
                ->where('commandes.updated_at', '>', $date_debut)
                ->where('commandes.updated_at', '<', $date_fin)
                ->whereIn('commandes.etat_commande', ['CANCELEDAG', 'CANCELEDLV', 'CANCELEDEV', 'CANCELEDRR', 'CANCELED'])
                ->where('commandes.id_client', $user->id)
                ->selectRaw("count(commandes.id_commande) as nbrColis")
                ->first();

                $suivieCommande = DB::table("commandes")
                ->where('commandes.updated_at', '>', $date_debut)
                ->where('commandes.updated_at', '<', $date_fin)
                ->whereIn('commandes.etat_commande', ['DMSUIVIE'])
                ->where('commandes.id_client', $user->id)
                ->selectRaw("count(commandes.id_commande) as nbrColis")
                ->first();



                $colisEnCours = DB::table("commandes")
                ->where('id_client', $user->id)
                ->whereIn('etat_commande', ['CANCELEDLV', 'CANCELEDAG', 'CANCELEDEV', 'CANCELED', 'CANCELEDRR', 'RELANCER', 'DMSUIVIE', 'ENROUTE', 'TRANSIT', 'REPORTED', 'ANNULER_CL', 'ANNULER', 'INHOUSE', 'HOME', 'ASSIGN', 'NOREPONSE', 'RETURNEDLV', 'RETURNEDEV', 'RETURNEDRR',])
                ->where('commandes.created_at', '>', $date_debut)
                ->where('commandes.created_at', '<', $date_fin)
                ->selectRaw("count(commandes.id_commande) as nbrColis")
                ->first();

                $colisTauxLivraison = DB::table("commandes")
                ->where('id_client', $user->id)
                ->whereNotIn('commandes.etat_commande', ['CREATED', 'CONFIRMED', 'PROCESSING', 'PICKUP'])
                ->where('commandes.created_at', '>', $date_debut)
                ->where('commandes.created_at', '<', $date_fin)
                ->selectRaw("count(commandes.id_commande) as nbrColis")
                ->first();



            $tauxLivraison = 0;
            if ($colisTauxLivraison->nbrColis != 0) {
                $tauxLivraison = ($deliverdCommande->nbrColis * 100) / $colisTauxLivraison->nbrColis;
            }       
            $colisFacture = DB::table("commandes")
                ->where('id_client', $user->id)
                ->whereIn('etat_commande', ['DELIVERED'])
                ->where('paid', 1)
                ->where('updated_at', '>', $date_debut)
                ->where('updated_at', '<', $date_fin)
                ->selectRaw("count(id_commande) as nbrColis")
                ->first();
            $nbrFacture = DB::table("factures")
                ->where('factures.id_client', $user->id)
                ->where('factures.created_at', '>', $date_debut)
                ->where('factures.created_at', '<', $date_fin)
                ->selectRaw("count(factures.id_facture) as nbrFacture")
                ->first();
            if (!$nbrFacture) {
                $nbrFacture['nbrFacture'] = 0;
            }
            $totalColis = DB::table("commandes")
                ->where('id_client', $user->id)
                ->where('created_at', '>', $date_debut)
                ->where('created_at', '<', $date_fin)
                ->selectRaw("count(id_commande) as nbrColis")
                ->first();



            return response()->json([
                'deliverdCommande' => $deliverdCommande,
                'returnedCommande' => $returnedCommande,
                'canceledCommande' => $canceledCommande,
                'suivieCommande' => $suivieCommande,

                'data2' => $RevenuOfMonth,
                'totalColis' => $totalColis,
                'colisEnCours' => $colisEnCours,
                'colisNonFacture' => $colisNonFacture,
                'colisFacture' => $colisFacture,
                'nbrFacture' => $nbrFacture,
                'tauxLivraison' => $tauxLivraison,
                'dateRange' => $date_debut->format('Y-m-d') . ' ~ ' . $date_fin->format('Y-m-d'),
                'fraisLivraison' => $fraisLivraison,
            ]);
        }
    }
}
