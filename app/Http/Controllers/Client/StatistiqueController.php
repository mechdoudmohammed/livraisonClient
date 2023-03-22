<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\Models\Commande;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class StatistiqueController extends Controller
{

    public function commandeStatistiquesRevenue(Request $request)
    {

        if (count($request->all()) > 1 && $request[0] !=null && $request[1]!=null) {
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

        if (count($request->all()) > 1 && $request[0] !=null && $request[1]!=null) {
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
        if (count($request->all()) > 1 && $request[0] !=null && $request[1]!=null) {
            $date_debut = Carbon::parse($request[0]);
            $date_fin = Carbon::parse($request[1]);
        } else {
            $date_debut = Carbon::now()->firstOfMonth();
            $date_fin = Carbon::now()->lastOfMonth();
        }


        $user = auth('sanctum')->user();
            $RevenuOfMonth = DB::table("commandes")
            ->whereIn('etat_commande', ['DELIVERED'])
            ->where('updated_at', '>', $date_debut)
            ->where('updated_at', '<', $date_fin)
            ->where('id_client', $user->id)
            ->selectRaw("sum(prix_commande) as somme")
            ->first();

        $Livraison = DB::table("commandes")
            ->join('villes', 'villes.id', 'commandes.id_ville')
            ->whereIn('etat_commande', ['DELIVERED', 'CANCELED', 'RETURNEDLV', 'RETURNEDAG', 'RETURNEDEV', 'RETURNED', 'RETURNEDRR'])
            ->where('commandes.updated_at', '>', $date_debut)
            ->where('commandes.updated_at', '<', $date_fin)
            ->where('id_client', $user->id)
            ->selectRaw('commandes.etat_commande,villes.prix_retour,villes.prix_refus,commandes.prix_livraison_final')
            ->get();

        $fraisLivraison = 0;
        foreach ($Livraison as $commande) {
            if ($commande->etat_commande == 'DELIVERED') {
                $fraisLivraison += $commande->prix_livraison_final;
            } elseif ($commande->etat_commande == 'RETURNEDAG'  || $commande->etat_commande == 'RETURNEDEV' || $commande->etat_commande == 'RETURNEDRR' || $commande->etat_commande == 'RETURNED') {
                $fraisLivraison += $commande->prix_retour;
            } elseif ($commande->etat_commande == 'CANCELED') {
                $fraisLivraison += $commande->prix_refus;
            }
        }














        $user = auth('sanctum')->user();
        if ($user->role == 'Client') {
            $commande = DB::table("commandes")
                ->where('id_client', $user->id)
                ->whereIn('etat_commande', ['DELIVERED', 'CANCELED', 'RETURNEDAG','RETURNEDLV','RETURNEDEV','RETURNEDRR','RETURNED', 'DMSUIVIE'])
                ->where('updated_at', '>', $date_debut)
                ->where('updated_at', '<', $date_fin)
                ->groupBy('etat_commande')
                ->selectRaw("count(id_commande) as nbrCommande,etat_commande")
                ->get();

            $colisEnCours = DB::table("commandes")
                ->where('id_client', $user->id)
                ->whereIn('etat_commande', ['CANCELED', 'RELANCER', 'DMSUIVIE', 'ENROUTE', 'TRANSIT', 'REPORTED', 'ANNULER_CL', 'ANNULER', 'INHOUSE', 'HOME', 'ASSIGN', 'NOREPONSE', 'RETURNEDLV','RETURNEDEV','RETURNEDRR',])
                ->where('updated_at', '>', $date_debut)
                ->where('updated_at', '<', $date_fin)
                ->selectRaw("count(id_commande) as nbrColis")
                ->first();


            $colisDelivred = DB::table("commandes")
                ->where('id_client', $user->id)
                ->whereIn('etat_commande', ['DELIVERED'])
                ->where('updated_at', '>', $date_debut)
                ->where('updated_at', '<', $date_fin)
                ->selectRaw("count(id_commande) as nbrColis")
                ->first();

            $colisTauxLivraison = DB::table("commandes")
                ->where('id_client', $user->id)
                ->whereNotIn('etat_commande', ['CREATED', 'CONFIRMED', 'PROCESSING', 'PICKUP'])
                ->where('updated_at', '>', $date_debut)
                ->where('updated_at', '<', $date_fin)
                ->selectRaw("count(id_commande) as nbrColis")
                ->first();
            $tauxLivraison = 0;
            if ($colisTauxLivraison->nbrColis != 0) {
                $tauxLivraison = ($colisDelivred->nbrColis * 100) / $colisTauxLivraison->nbrColis;
            }




            $colisNonFacture = DB::table("commandes")
                ->where('id_client', $user->id)
                ->whereIn('etat_commande', ['DELIVERED'])
                ->where('paid', 0)
                ->where('updated_at', '>', $date_debut)
                ->where('updated_at', '<', $date_fin)
                ->selectRaw("count(id_commande) as nbrColis")
                ->first();
            $colisFacture = DB::table("commandes")
                ->where('id_client', $user->id)
                ->whereIn('etat_commande', ['DELIVERED'])
                ->where('paid', 1)
                ->where('updated_at', '>', $date_debut)
                ->where('updated_at', '<', $date_fin)
                ->selectRaw("count(id_commande) as nbrColis")
                ->first();
            $nbrFacture = DB::table("commandes")
                ->join('factures', 'commandes.id_facture', 'factures.id_facture')
                ->where('commandes.id_client', $user->id)
                ->where('factures.updated_at', '>', $date_debut)
                ->where('factures.updated_at', '<', $date_fin)
                ->groupBy('commandes.id_facture')
                ->selectRaw("count(distinct commandes.id_facture) as nbrFacture")
                ->first();

            if (!$nbrFacture) {
                $nbrFacture['nbrFacture'] = 0;
            }

            $totalColis = DB::table("commandes")
                ->where('id_client', $user->id)
                ->where('updated_at', '>', $date_debut)
                ->where('updated_at', '<', $date_fin)
                ->selectRaw("count(id_commande) as nbrColis")
                ->first();



            return response()->json([
                'data' => $commande,
                'data2' => $RevenuOfMonth,
                'totalColis' => $totalColis,
                'colisEnCours' => $colisEnCours,
                'colisNonFacture' => $colisNonFacture,
                'colisFacture' => $colisFacture,
                'nbrFacture' => $nbrFacture,
                'tauxLivraison' => $tauxLivraison,
                'dateRange' => $date_debut->format('Y-m-d').' ~ '.$date_fin->format('Y-m-d'),
                'fraisLivraison' => $fraisLivraison,
            ]);
        }
    }
}
