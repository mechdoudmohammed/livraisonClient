<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Commande;
use App\Models\Employe;
use App\Models\Store;
use App\Models\Ville;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommandesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 100; $i++) {
            Commande::create([

                "id_commande" =>  strtoupper(Str::random(6)),
                "id_ville" => Ville::all()->random()->id,
                "nom_client_commande" => strtoupper(Str::random(6)),
                "adresse_client_commande" => strtoupper(Str::random(6)),
                "telephone_client_commande" => strtoupper(Str::random(10)),
                "prix_commande" => 55,
                "prix_livraison_final" => 99,
                "etat_commande" => "DELIVERED",
                "id_client" => Client::all()->random()->id,
                "additional_commentaire" => strtoupper(Str::random(6)),
                "type_autorisation" => 'allow',
                "type_commande"=>'ramassage',
                'id_employe_client' => Employe::all()->random()->id,
            ]);

        }

        
    }
}
