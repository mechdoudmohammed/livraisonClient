<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EmployeController extends Controller
{
    public function index()
    {
        $user = auth('sanctum')->user();
        if ($user->role == 'Ramasseur' && $user->statut == 'Active') {
            $livreur = Employe::join('villes','employes.id_ville','=','villes.id')
            ->where(function ($query) use ($user) {
                $query->where('superviseur',$user->id)
                ->Orwhere('employes.id',$user->id);
            })

            ->whereIn('role',['Livreur','Ramasseur'])
            ->select('employes.username','employes.id','villes.nom_ville','employes.nom','employes.prenom','employes.statut','employes.telephone')
            ->orderby('employes.updated_at','desc')
            ->get();
            return response()->json([
                'data' => $livreur,
            ]);
        }
       
    }
    public function BlockEmploye($id)
    {
        $employe = Employe::find($id);
        if ($employe->statut == "Inactive") {
            $employe->statut = "Active";
            $message = "Employe Activer successfully";
        } else {
            $employe->statut = "Inactive";
            $message = "Employe Blocker successfully";
        }
        $employe->save();
        return response()->json([
            'message' => $message
        ]);
    }
    public function modifier(Request $request)
    {
        $this->validate($request, [
            'nom' => 'required|String',
            'prenom' => 'required|String',
            'adresse' => 'required|String',
            'telephone' => 'required|numeric',
            'sexe' => 'required|String',
            'email' => 'required|email|unique:employes,email,' . $request->id,



        ]);
        $employe = Employe::find($request->id);
        $employe->nom = $request->nom;
        $employe->prenom = $request->prenom;
        $employe->adresse = $request->adresse;
        $employe->telephone = $request->telephone;
        $employe->email = $request->email;
        $employe->sexe = $request->sexe;
        $employe->cin = $request->cin;
        $employe->statut = $request->statut;
        $employe->etat = $request->etat;
        $employe->observation = $request->observation;

        $employe->save();
        return response()->json([
            'message' => 'Employe modifier successfully'
        ]);
    }
    public function show($id)
    {
        $user = auth('sanctum')->user();
        if ($user->role == 'SuperAdmin' && $user->statut == 'Active') {
            $employe = Employe::leftjoin('employes as superviseurs','superviseurs.id','=','employes.superviseur')
            ->leftjoin('employes as ajoute_par','ajoute_par.id','=','employes.ajoute_par')
            ->where('employes.id',$id)
            ->select('employes.*','superviseurs.nom as nomsuperviseur','ajoute_par.nom as ajoute_par')
            ->first()
            ;
            return response()->json([
                'data' => $employe,
            ]);
        }
        $employe = Employe::find($id);
        return response()->json([
            'data' => $employe,
        ]);
    }
    public function inscription(Request $request)
    {
        $user = auth('sanctum')->user();
        if ($user->role == 'Ramasseur' && $user->statut == 'Active') {
            $this->validate($request, [
                'id_ville' => 'required|integer',
                'adresse' => 'required|String',
                'prenom' => 'required|String',
                'nom' => 'required|String',
                'telephone' => 'required|required|regex:/(0)[0-9]{9}/',
                'cin' => 'required|String',
                'username' => 'required|String|unique:employes,username',
                'password' => 'required|String',
                'cin_photo' => 'required|image|mimes:jpeg,jpg,png,gif|max:5000',

            ]);
            if ($request->hasFile('cin_photo')) {
                $file=$request->file('cin_photo');
                $file_extension_img=$file->getClientOriginalExtension();
                $file_name = time().$request->username.".".$file_extension_img;
                Storage::put('public/utilisateur/livreur/' . $file_name, file_get_contents($file));


                $statut = Employe::create([
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'adresse' => $request->adresse,
                    'telephone' => $request->telephone,
                    'id_bank' => $request->id_bank,
                    'ribBank' => $request->ribBank,
                    'id_ville' => $request->id_ville,
                    'id_zone' => $user->id_zone,
                    'email' => $request->username,
                    'username' => $request->username,
                    'cin' =>  $request->cin,
                    'cin_photo' =>  $file_name,
                    'role' =>  'Livreur',
                    'statut' =>  'Inactive',
                    'superviseur' =>  $user->id,
                    'ajoute_par' =>  $user->id,
                    'password' => Hash::make($request->password),
                ]);
            } else {
                $statut=false;
            }

            if ($statut) {
                return response()->json(['message' => 'Le livreur a été ajouté successfully']);
            } else {
                return response()->json(['message' => 'Erreur']);
            }
        }
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ]);
        $employe = Employe::where('email', $request->email)->orWhere('username', $request->email)->first();

        if (!$employe || !Hash::check($request->password, $employe->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les informations d\'identification fournies sont incorrectes.'],
            ]);
        }
        if ($employe->statut == 'Inactive') {
            return response()->json(
                [
                    "data" => "bloque"
                ]

            );;
        }
        return $employe->createToken($request->device_name)->plainTextToken;
    }
    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();
        return response()->json(['msg' => 'Logout Successfull']);
    }
    public function destroy($id)
    {
        $employe = Employe::find($id);
        $employe->delete();
        return response()->json("Record deleted!");
    }
    public function listClient()
    {
        $employe = auth('sanctum')->user();

        return response()->json([
            'data' => $employe,
        ]);
    }
}
