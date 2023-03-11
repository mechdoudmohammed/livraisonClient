<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVillesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villes', function (Blueprint $table) {
            $table->id();
            $table->string('nom_ville',50);
            $table->string('pref_ville',10);
            $table->double('prix_livraison')->nullable();
            $table->double('prix_refus')->nullable();
            $table->double('prix_retour')->nullable();

            $table->double('prix_livraison_ramasseur_ville')->nullable();
            $table->double('prix_refus_ramasseur_ville')->nullable();
            $table->double('prix_retour_ramasseur_ville')->nullable();



            $table->string('statut',20)->default('Inactive');
            $table->unsignedBigInteger('id_zone')->nullable();
            $table->foreign('id_zone')->references('id')->on('zones');


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
        Schema::dropIfExists('villes');
    }
}
