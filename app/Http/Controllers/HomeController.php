<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\HistoriqueCommande;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class HomeController extends Controller
{

    public function index()
    {
        return view('home.vue');
    }

    public function getBank()
    {
        $banks = DB::table('banques')
            ->select('nomBank', 'id as id_bank')
            ->get();
        return response()->json([
            'data' =>  $banks,

        ]);
    }

    public function getVillesVisiteur(Request $request)
    {
        $villes = DB::table('villes')
            ->Where('villes.nom_ville', 'LIKE', "%{$request->ville}%")
            ->where('villes.statut','Active')
            ->selectRaw('nom_ville,prix_livraison,prix_retour')->paginate();
        return response()->json([
            'data' => $villes,
        ]);
    }
    public function historiqueCommande($id)
    {

        $commandes = DB::table('historiquecommandes')
            ->leftjoin('employes', 'historiquecommandes.id_employe', '=', 'employes.id')
            ->leftjoin('clients', 'historiquecommandes.id_client', '=', 'clients.id')
            ->join('commandes', 'commandes.id_commande', '=', 'historiquecommandes.id_commande')

            ->join('villes', 'commandes.id_ville', 'villes.id')
            ->where('historiquecommandes.id_commande', $id)
            ->whereIn('historiquecommandes.etat_commande', ['ENROUTE', 'COMMENTAIRE', 'ANNULER', 'ANNULER_CL', 'HOME', 'ASSIGN', 'REPORTED', 'DELIVERED', 'TRANSIT', 'NOREPONSE', 'RETURNEDLV', 'CANCELED', 'ANNULER', 'ANNULER_CL', 'RELANCER', 'CHANGERPRIX', 'DMSUIVIE'])
            ->select('historiquecommandes.etat_commande', 'villes.nom_ville', 'clients.username as clientUsername', 'historiquecommandes.reported_date', 'historiquecommandes.commentaire_commande', 'employes.username', 'historiquecommandes.updated_at',)
            ->orderBy('historiquecommandes.updated_at', 'asc')
            ->get();


        return response()->json([
            'data' => $commandes,
        ]);
    }

    public function callLogs(Request $request)
    {

        $token = PersonalAccessToken::findToken($request->header('Authorization'));
        $user = $token->tokenable;

        for ($i = count($request->all())-1; $i >= 0; $i--) {

            $commande = Commande::where('livre_par', $user->id)

            ->where(function ($query) use ($request,$i) {
                $query->where('telephone_client_commande', $request[$i]['number'])
                ->orwhere('telephone_client_commande','0'.substr($request[$i]['number'],4));
            })
            ->whereIn('etat_commande', ['ASSIGN', 'REPORTED','HOME', 'TRANSIT', 'NOREPONSE', 'RELANCER', 'CHANGERPRIX', 'DMSUIVIE'])->first();
            if ($commande) {
                $listLogCall = HistoriqueCommande::where('id_commande', $commande->id_commande)
                    ->where('etat_commande', 'callLog')
                    ->where('dateCall', $request[$i]['dateCall'])
                    ->where('durationCall', $request[$i]['durationCall'])
                    ->where('typeCall', $request[$i]['typeCall'])
                    ->first();

                if (!$listLogCall) {
                    HistoriqueCommande::create([
                        "id_commande" => $commande->id_commande,
                        "etat_commande" => 'callLog',
                        "id_client" => $commande->id_client,
                        "dateCall" => $request[$i]['dateCall'],
                        "durationCall" => $request[$i]['durationCall'],
                        "typeCall" =>  $request[$i]['typeCall'],
                        "id_employe" => $user->id,

                    ]);
                    $commande->updated_at=Carbon::now();
                    $commande->save();
                }
            }
        }

        return 'goood';

    }
}
