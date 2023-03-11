<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatehistoriqueCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historiqueCommandes', function (Blueprint $table) {
            $table->id();
            $table->string('etat_commande',100)->nullable();
            $table->string('commentaire_commande')->nullable();
            $table->string('evidence')->nullable();
            $table->string('id_commande',100)->nullable();

            $table->string('dateCall',60)->nullable();
            $table->string('typeCall',60)->nullable();
            $table->string('durationCall',20)->nullable();


            $table->date('reported_date')->nullable();
            $table->foreign('id_commande')->references('id_commande')->on('commandes')->onDelete('cascade');
            $table->unsignedBigInteger('id_client')->nullable();
            $table->foreign('id_client')->references('id')->on('clients');
            $table->unsignedBigInteger('id_employe')->nullable();
            $table->foreign('id_employe')->references('id')->on('employes');
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->foreign('id_admin')->references('id')->on('users');

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
        Schema::dropIfExists('historiqueCommandes');
    }
}
