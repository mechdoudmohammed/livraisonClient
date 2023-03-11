<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecteursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secteurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom_secteur',50);
            $table->string('pref_secteur',20);

            $table->unsignedBigInteger('responsable_secteur')->nullable();
            $table->foreign('responsable_secteur')->references('id')->on('employes');

            $table->unsignedBigInteger('livreur_secteur')->nullable();
            $table->foreign('livreur_secteur')->references('id')->on('employes');

            $table->string('statut',20)->default('Inactive');
            $table->unsignedBigInteger('id_ville')->nullable();
            $table->foreign('id_ville')->references('id')->on('villes');


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
        Schema::dropIfExists('secteurs');
    }
}
