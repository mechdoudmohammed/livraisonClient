<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->string('id_commande')->primary();
            $table->string('nom_client_commande',100);
            $table->string('adresse_client_commande');
            $table->string('telephone_client_commande',20);
            $table->double('prix_commande');
            $table->string('etat_commande',60);
            $table->string('type_autorisation',20)->default('allow');
            $table->double('prix_livraison_final');
            $table->boolean('paid')->default(0);
            $table->string('additional_commentaire',150)->nullable();
            $table->string('id_package')->nullable();
            $table->string('type_commande',100)->default('ramassage');

            $table->unsignedBigInteger('id_ville')->nullable();
            $table->foreign('id_ville')->references('id')->on('villes');

            $table->unsignedBigInteger('id_store')->nullable();
            $table->foreign('id_store')->references('id')->on('stores');


            $table->unsignedBigInteger('id_client')->nullable();
            $table->foreign('id_client')->references('id')->on('clients');

            $table->unsignedBigInteger('id_employe_client')->nullable();
            $table->foreign('id_employe_client')->references('id')->on('clients');

            $table->string('id_bon_retour_client',100)->nullable();
            $table->foreign('id_bon_retour_client')->references('id_bon_retour_client')->on('bonretourclients');

            $table->string('id_facture',100)->nullable();
            $table->foreign('id_facture')->references('id_facture')->on('factures');

            $table->string('id_facture_ramasseur',100)->nullable();
            $table->foreign('id_facture_ramasseur')->references('id_facture')->on('factures');

            $table->string('id_facture_ramasseur_daily',100)->nullable();
            $table->foreign('id_facture_ramasseur_daily')->references('id_facture')->on('factures');

            $table->string('id_facture_ramassage',100)->nullable();
            $table->foreign('id_facture_ramassage')->references('id_facture')->on('factures');

             $table->string('id_bon_envoi',100)->nullable();
            // $table->foreign('id_bon_envoi')->references('id_bon_envoi')->on('bonenvois');

            $table->string('id_bon_reception',100)->nullable();
            $table->string('id_bon_retour',100)->nullable();
            
            // $table->foreign('id_bon_reception')->references('id_bon_reception')->on('bonreceptions');

            $table->unsignedBigInteger('ramasser_par')->nullable();
            $table->foreign('ramasser_par')->references('id')->on('employes');

            $table->unsignedBigInteger('responsable')->nullable();
            $table->foreign('responsable')->references('id')->on('employes');

            $table->unsignedBigInteger('livre_par')->nullable();
            $table->foreign('livre_par')->references('id')->on('employes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commandes');
    }
}
