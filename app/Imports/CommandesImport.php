<?php

namespace App\Imports;

use App\Models\Commande;
use App\Models\HistoriqueCommande;
use App\Models\Ville;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithLimit;

class CommandesImport implements ToArray, SkipsEmptyRows, WithStartRow, WithValidation, WithMultipleSheets, WithLimit
{
    private $data;
    private $store;

    public function __construct($store)
    {
        $this->data = [];
        $this->store = $store;
    }
    public function array(array $rows)
    {
        $id_employe_client = null;
        if (count($rows) <= 100) {
            foreach ($rows as $row) {
                if ($row[0] != null) {
                    $this->data[] = array(
                        'nom_client_commande' => $row[0],
                        'adresse_client_commande'    => $row[2],
                        'telephone_client_commande' => $row[3],
                        'prix_commande'    => $row[4],
                        'type_autorisation'    => $row[5],
                    );
                    $user = auth('sanctum')->user();
                    $Balance = DB::table("factures")
                    ->where('factures.statut_facture', 'NOTPAID')
                    ->where('id_client',  $user->id)
                    ->selectRaw("IFNULL(sum(factures.total_facture - factures.frais_livraison_facture), 0) as balance")
                    ->first();
            
                    if ($Balance->balance < -1000) {
                        return response()->json([
                            'message' => 'Insufficient balance',
                            'balance'=>$Balance->balance
                        ]);
                    }
            
                    $ville = DB::table('villes')
                        ->where("villes.nom_ville",$row[1])
                        ->select('villes.id', 'prix_livraison')->first();
                    $ville = Ville::where('id', $ville->id)->first();
                    $id_commande = $ville->pref_ville . Carbon::now()->format('d') . Carbon::now()->format('m') . Carbon::now()->format('y') . $user->id . chr(rand(65, 90)) . strtoupper(Str::random(3));

                    if ($user->role == 'EmployeClient') {
                        $id_client = $user->superviseur;
                        $id_employe_client = $user->id;
                    } else if ($user->role == 'Client') {
                        $id_client = $user->id;
                    }
                    Commande::create([
                        "id_commande" => $id_commande,
                        "id_ville" =>  $ville->id,
                        "id_store" => $this->store,
                        "nom_client_commande" => $row[0],
                        "adresse_client_commande" => $row[2],
                        "telephone_client_commande" => $row[3],
                        "prix_commande" => $row[4],
                        "etat_commande" => "CREATED",
                        'type_autorisation' => $row[5],
                        "id_client" => $id_client,
                        "prix_livraison_final" => $ville->prix_livraison,
                        'id_employe_client' => $id_employe_client,
                    ]);
                    HistoriqueCommande::create([
                        "id_commande" =>  $id_commande,
                        "etat_commande" => 'CREATED',
                        "id_client" => $user->id,
                    ]);
                }
            }
            $this->data = ['Les commandes created successfully'];
        } else {
            $this->data = ['Le fichier depasse 100 row'];
        }
    }
    public function getArray(): array
    {

        return $this->data;
    }
    public function startRow(): int
    {
        return 2;
    }
    use Importable;
    public function rules(): array
    {
        $ville = DB::table('villes') ->where('villes.statut','Active')->pluck('nom_ville')->toArray();
       
 
        return [
            '0' => 'required|string',
            '1' => ['required', 'string', Rule::in(array_map('strtolower', $ville))],
            '2' => 'required|string',
            '3' => 'required|numeric',
            '4' => 'required|numeric',
            '5' => ['required', Rule::in(['allow', 'deny']),],

        ];
    }
    public function customValidationMessages()
    {
        return [
            '1.in' => 'Please check City name or (write City in small character)',
            '5.in' => 'The field need to be \'allow\' or \'deny\'',
        ];
    }
    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }
    public function limit(): int
    {
        return 110;
    }
}
