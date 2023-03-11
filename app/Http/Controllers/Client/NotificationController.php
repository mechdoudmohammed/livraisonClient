<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\Models\Commande;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('sanctum')->user();
        if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
$notification = DB::table('notifications')
            ->where('Affichage', 'notSeen')
            ->leftjoin('clients', 'notifications.id_client', '=', 'clients.id')
            ->leftJoin('commandes', 'notifications.id_commande', '=', 'commandes.id_commande')
            ->whereIn('notifications.titre', ['Demande Suivie'])
            ->where('notifications.Affichage', 'notSeen')
            ->where('notifications.id_client', $user->id)
            ->orderBy('notifications.updated_at','desc')
             ->selectRaw('notifications.updated_at,notifications.titre,notifications.id as id,notifications.description,notifications.Affichage,notifications.id_reclamation,notifications.id_commande,notifications.id_facture,notifications.id_client')
            ->get();

        return response()->json($notification);

        }
        
    }

    public function checkNotification()
    {

        $notification = DB::table('notifications')
            ->where('Affichage', 'notSeen')
            ->leftjoin('clients', 'notifications.id_client', '=', 'clients.id')
            ->leftJoin('commandes', 'notifications.id_commande', '=', 'commandes.id_commande')
            // ->select('notifications.*', 'notifications.id as id', 'clients.nom', 'clients.prenom', 'reservations.num_reservation')
            ->get();


        return response()->json(['data' => $notification]);
    }

    public function show($id)
    {
        $notification = Notification::find($id);

        return response()->json([
            'data' => $notification,

        ]);
    }


    public function modifier(Request $request)
    {

        $notification = Notification::find($request->id);

        $notification->Affichage          = "Non";
        $notification->save();
        return response()->json([
            'message' => 'notification update successfully'
        ]);
    }
    public function importantNotification(){
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
      
    }
    public function destroy($id)
    {

        $user = auth('sanctum')->user();
        if ($user->role == 'Client'   && $user->statut == 'Active') {
            $notification = Notification::where('notifications.id', $id)->where('notifications.id_client', $user->id)->first();
            $notification->delete();
            return response()->json("Record deleted!");
        }
    }
}
