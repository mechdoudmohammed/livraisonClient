<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\Models\Commande;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
                $notification = DB::table('notifications')
                    ->where('Affichage', 'notSeen')
                    ->leftjoin('clients', 'notifications.id_client', '=', 'clients.id')
                    ->leftJoin('commandes', 'notifications.id_commande', '=', 'commandes.id_commande')
                    ->whereIn('notifications.titre', ['Demande Suivie'])
                    ->where('notifications.Affichage', 'notSeen')
                    ->where('notifications.id_client', $user->id)
                    ->orderBy('notifications.updated_at', 'desc')
                    ->selectRaw('notifications.updated_at,notifications.titre,notifications.id as id,notifications.description,notifications.Affichage,notifications.id_reclamation,notifications.id_commande,notifications.id_facture,notifications.id_client')
                    ->get();
               

                return response()->json($notification);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function getCountDMsuivie()
    {
        $user = auth('sanctum')->user();
        if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
            $CountDMsuivie = DB::table('commandes')
            ->where('commandes.etat_commande', 'DMSUIVIE')
            ->where('commandes.id_client', $user->id)
            ->selectRaw('count(commandes.id_commande) as nbrColis')
            ->first();
            return response()->json(['data' => $CountDMsuivie]);

        }
      
    }
    public function checkNotification()
    {
        try {
            $notification = DB::table('notifications')
                ->where('Affichage', 'notSeen')
                ->leftjoin('clients', 'notifications.id_client', '=', 'clients.id')
                ->leftJoin('commandes', 'notifications.id_commande', '=', 'commandes.id_commande')
                // ->select('notifications.*', 'notifications.id as id', 'clients.nom', 'clients.prenom', 'reservations.num_reservation')
                ->get();



            return response()->json(['data' => $notification]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }

    public function show($id)
    {
        try {
            $notification = Notification::find($id);
            return response()->json([
                'data' => $notification,

            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }


    public function modifier(Request $request)
    {
        try {
            $notification = Notification::find($request->id);
            $notification->Affichage          = "Non";
            $notification->save();
            return response()->json([
                'message' => 'notification update successfully'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function importantNotification()
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
                $notification = DB::table('notifications')
                    ->where('Affichage', 'notSeen')
                    ->leftjoin('clients', 'notifications.id_client', '=', 'clients.id')
                    ->leftJoin('commandes', 'notifications.id_commande', '=', 'commandes.id_commande')
                    ->whereIn('notifications.titre', ['Demande Suivie'])
                    ->where('notifications.Affichage', 'notSeen')
                    ->where('notifications.id_client', $user->id)
                    ->selectRaw('count(notifications.id) as nbrNotification')
                    ->first();

                return response()->json([
                    'data' => $notification
                ]);
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
            if ($user->role == 'Client'   && $user->statut == 'Active') {
                $notification = Notification::where('notifications.id', $id)->where('notifications.id_client', $user->id)->first();
                $notification->delete();
                return response()->json("Record deleted!");
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
}
