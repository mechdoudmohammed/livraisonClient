<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Commande;
use App\Models\Article;
use App\Models\Facture;
use App\Models\Message;
use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Throwable;

class ReclamationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                if ($request->selected_option == "id_facture" && $request->valeur_recherche != '') {
                    $reclamations = DB::table('reclamations')
                        ->where('reclamations.id_client', $user->id)
                        ->where('reclamations.id_facture', 'LIKE', "%{$request->valeur_recherche}%")
                        ->orderBy('reclamations.updated_at', 'desc')
                        ->selectRaw('reclamations.id_reclamation,reclamations.updated_at,reclamations.statut_reclamation,reclamations.object_reclamation,reclamations.id_facture,reclamations.id_commande')
                        ->paginate($_GET['count_nbr']);
                } else {
                    $reclamations = DB::table('reclamations')
                        ->where('reclamations.id_client', $user->id)
                        ->orderBy('reclamations.updated_at', 'desc')
                        ->selectRaw('reclamations.id_reclamation,reclamations.updated_at,reclamations.statut_reclamation,reclamations.object_reclamation,reclamations.id_facture,reclamations.id_commande')
                        ->paginate($_GET['count_nbr']);
                }
                return response()->json([
                    'data' => $reclamations,
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }

    public function show($id)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $reclamations = DB::table('reclamations')
                    ->where('reclamations.id_reclamation', $id)
                    ->select('reclamations.id_reclamation', 'reclamations.object_reclamation', 'reclamations.id_facture', 'reclamations.id_commande')
                    ->first();
                return response()->json([
                    'data' => $reclamations,
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
        $this->validate($request, [
                    "Type_reclamation" => "required",
                    "object_reclamation" => 'required|string|max:200',
                    "message_reclamation" => 'required|string|max:255',

                ]);
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
              
                $id_facture = null;
                $id_commande = null;
                if ($request->Type_reclamation == 'Order') {
                    $commande = Commande::where('id_commande', $request->id)->where('id_client', $user->id)->first();

                    if ($commande && $request->id == $commande->id_commande) {
                        $id_commande = $request->id;
                    } else {
                        return response()->json([
                            'message' => 'Order does not exist'
                        ]);
                    }
                } else if ($request->Type_reclamation == 'Invoice') {
                    $facture = Facture::where('factures.id_facture', $request->id)->join('commandes', 'commandes.id_facture', 'factures.id_facture')->where('commandes.id_client', $user->id)->first();

                    if ($facture && $request->id == $facture->id_facture) {
                        $id_facture = $request->id;
                    } else {
                        return response()->json([
                            'message' => 'Invoice does not exist'
                        ]);
                    }
                    $id_facture = $request->id;
                }
                $id_reclamation = strtoupper(Str::random(6)) . time();
                $statut = Reclamation::create([
                    "id_reclamation" => $id_reclamation,
                    "object_reclamation" => $request->object_reclamation,
                    "statut_reclamation" => "En Traitement",
                    "id_facture" => $id_facture,
                    "id_commande" => $id_commande,
                    "id_client" => $user->id,
                ]);
                $statut = Message::create([
                    "id_reclamation" => $id_reclamation,
                    "id_client" => $user->id,
                    "message" => $request->message_reclamation,
                ]);
                if ($statut) {
                    return response()->json([
                        'message' => 'Claim created successfully'
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
}
