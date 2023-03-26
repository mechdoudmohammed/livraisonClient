<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatebonretourclientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonRetourClients', function (Blueprint $table) {
            $table->string('id_bon_retour_client',100)->primary();
            $table->string('statut_bonRetourClient',100);

            $table->integer('nbrColis_bonRetourClient')->nullable();
           
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
        Schema::dropIfExists('bonRetourClients');
    }
}
