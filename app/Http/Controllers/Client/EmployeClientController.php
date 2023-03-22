<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\Models\Commande;
use App\Models\Article;
use App\Models\HistoriqueCommande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CommandesImport;
use App\Models\Client;
use App\Models\DetailsCommandes;
use App\Models\Store;
use App\Models\Ville;
use Laravel\Sanctum\PersonalAccessToken;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Throwable;

class EmployeClientController extends Controller
{
    public function index()
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {

                $clients = DB::table('clients')
                    ->join('villes', 'clients.id_ville', '=', 'villes.id')
                    ->leftjoin('zones', 'villes.id_zone', '=', 'zones.id')
                    ->where('clients.superviseur', $user->id)
                    ->selectRaw('clients.stock,clients.id,clients.nom,clients.prenom,clients.telephone,clients.username,clients.statut,zones.nom_zone')
                    ->orderby('clients.updated_at', 'desc')
                    ->paginate();
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
    public function store(Request $request)
    {
        $this->validate($request, [
            'nom' => 'required|String',
            'prenom' => 'required|String',
            'adresse' => 'nullable|String',
            'telephone' => 'required|String',
            'cin' => 'nullable|String',
            'ribBank' => 'nullable|String',
            'username' => 'required|String',
            'id_ville' => 'required',
            'email' => 'required|unique:clients,email|email',
            'password' => 'required|String',
        ]);
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {


                if ($request->stock == 'true') {
                    $request->stock = 1;
                } else if ($request->stock == 'false') {
                    $request->stock = 0;
                }
                $statut = Client::create([
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'adresse' => $user->adresse,
                    'telephone' => $request->telephone,
                    'email' => $request->email,
                    'cin' =>  $request->cin,
                    'role' => 'EmployeClient',
                    'company' => $user->company,
                    'website' => $user->website,
                    'stock' => $user->stock,
                    'id_bank' => $request->id_bank,
                    'ribBank' => $request->ribBank,
                    'username' =>  $request->username,
                    'id_ville' => $request->id_ville,
                    'superviseur' =>  $user->id,
                    'password' => Hash::make($request->password),
                ]);

                if ($statut) {
                    event(new Registered($request));
                    return response()->json([
                        'message' => 'Employe Created successfully'
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
    public function BlockEmployeClient($id)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $client = Client::where('clients.superviseur', $user->id)->where('clients.id', $id)->first();
                if ($client->statut == "Inactive") {
                    $client->statut = "Active";
                    $message = "Client Activer successfully";
                } else {
                    $client->statut = "Inactive";
                    $message = "Client Blocker successfully";
                }
                $client->save();
                return response()->json([
                    'message' => $message
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
                $client = Client::join('villes', 'clients.id_ville', '=', 'villes.id')
                    ->where('clients.id', $id)
                    ->where('clients.superviseur', $user->id)
                    ->selectRaw('clients.nom,clients.prenom,clients.telephone,clients.email,clients.cin,clients.ribBank,clients.bankName,clients.username,villes.id as ville, villes.nom_ville')
                    ->first();
                return response()->json([
                    'data' => $client
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }










    public function updateCommande(Request $request)
    {
    }
    public function destroy($id)
    {
    }
    public function getPackage(Request $request)
    {
    }
    public function historiqueCommande($id)
    {
    }
    public function changeStatutCommande(Request $request)
    {
    }
}
