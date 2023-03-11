<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('titre',150);
            $table->string('description');
            $table->string('Affichage',20);

            $table->string('id_reclamation',100)->nullable();
            $table->foreign('id_reclamation')->references('id_reclamation')->on('reclamations');

            $table->string('id_commande',100)->nullable();
            $table->foreign('id_commande')->references('id_commande')->on('commandes');

            $table->string('id_facture',100)->nullable();
            $table->foreign('id_facture')->references('id_facture')->on('factures');

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
        Schema::dropIfExists('notifications');
    }
}
