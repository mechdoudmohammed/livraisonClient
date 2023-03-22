<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Commande;
use App\Models\Article;
use App\Models\Facture;
use App\Models\Reclamation;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Throwable;

class StoreController extends Controller
{
    public function index()
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
                $stores = DB::table('stores')
                    ->join('villes', 'villes.id', 'stores.id_ville')
                    ->where(function ($query) use ($user) {
                        $query->where('stores.id_client', $user->id)
                            ->orwhere('stores.id_client', $user->superviseur);
                    })
                    ->where('stores.statut_store', "Active")
                    ->orderBy('stores.updated_at', 'desc')
                    ->selectRaw('stores.id,stores.statut_store,stores.favorite_store,
                stores.nom_store,stores.siteweb_store,stores.telephone_store,stores.adresse_store,villes.nom_ville as ville')
                    ->get();
                return response()->json([
                    'data' => $stores,
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function BlockStore($id)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                $store = Store::where('stores.id', $id)->where('stores.id_client', $user->id)->first();
                if ($store->statut_store == "Inactive") {
                    $store->statut_store = "Active";
                    $message = "Store Activer successfully";
                } else {
                    $store->statut_store = "Inactive";
                    $message = "Store Blocker successfully";
                }
                $store->save();
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
    public function getStore(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if (($user->role == 'Client' || $user->role == 'EmployeClient')  && $user->statut == 'Active') {
                $stores = DB::table('stores')
                    ->join('villes', 'villes.id', 'stores.id_ville')
                    ->where(function ($query) use ($user) {
                        $query->where('stores.id_client', $user->id)
                            ->orwhere('stores.id_client', $user->superviseur);
                    })
                    ->orderBy('stores.updated_at', 'desc')
                    ->selectRaw('stores.id,stores.updated_at,stores.statut_store,stores.favorite_store,
                stores.nom_store,stores.siteweb_store,stores.telephone_store,stores.adresse_store,villes.nom_ville as ville')
                    ->paginate($_GET['count_nbr']);
                return response()->json([
                    'data' => $stores,
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
                $store = DB::table('stores')
                    ->join('villes', 'villes.id', 'stores.id_ville')
                    ->where('stores.id', $id)
                    ->select('stores.id', 'stores.nom_store', 'stores.siteweb_store', 'stores.telephone_store', 'stores.adresse_store', 'stores.id_ville', 'villes.nom_ville as ville')
                    ->first();
                return response()->json([
                    'data' => $store,
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
            "nom_store" => 'required|string|max:100',
            "siteweb_store" => 'required|string|max:155',
            "telephone_store" => "required",
            "adresse_store" => 'required|string|max:155',
            "ville" => 'required',
        ]);
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {


                $statut = Store::create([
                    "nom_store" => $request->nom_store,
                    "siteweb_store" => $request->siteweb_store,
                    "telephone_store" => $request->telephone_store,
                    "adresse_store" => $request->adresse_store,
                    "id_ville" => $request->ville['id'],
                    "id_client" => $user->id,
                ]);
                if ($statut) {
                    return response()->json([
                        'message' => 'Store created successfully'
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
    public function updateStore(Request $request)
    {
        $this->validate($request, [
            "nom_store" => 'required|string|max:100',
            "siteweb_store" => 'required|string|max:155',
            "telephone_store" => "required",
            "adresse_store" => 'required|string|max:155',
            "ville" => 'required',
        ]);
        try {
            $user = auth('sanctum')->user();

            if ($user->role == 'Client' && $user->statut == 'Active') {
                $store = Store::where('id', $request->id)->where('stores.id_client', $user->id)->first();

                $store->nom_store = $request->nom_store;
                $store->siteweb_store = $request->siteweb_store;
                $store->telephone_store = $request->telephone_store;
                $store->id_ville = $request->ville['id'];

                $store->save();
                return response()->json([
                    'message' => 'Store modifier successfully'
                ]);
            }
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erreur'
            ]);
        }
    }
    public function changeFavorite($id)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user->role == 'Client' && $user->statut == 'Active') {
                Store::where('stores.id_client', $user->id)->update(['favorite_store' => 0]);


                $store = Store::where('id', $id)->where('stores.id_client', $user->id)->first();
                $store->favorite_store = 1;
                $statut = $store->save();
                if ($statut) {
                    return response()->json([
                        'message' => 'Store change successfully'
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
