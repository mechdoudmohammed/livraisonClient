<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatemanualFacturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manualFactures', function (Blueprint $table) {
            $table->id();

            $table->string('type_facture',100)->nullable();
            $table->double('total_commande');
            $table->string('statut_commande',100)->nullable();
            
            $table->string('id_facture',100)->nullable();
            $table->foreign('id_facture')->references('id_facture')->on('factures');

            $table->string('id_commande',100)->nullable();
            $table->foreign('id_commande')->references('id_commande')->on('commandes');

            $table->unsignedBigInteger('id_employe')->nullable();
            $table->foreign('id_employe')->references('id')->on('employes');
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->foreign('id_admin')->references('id')->on('users');
            $table->unsignedBigInteger('id_client')->nullable();
            $table->foreign('id_client')->references('id')->on('clients');


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
        Schema::dropIfExists('manualFactures');
    }
}
