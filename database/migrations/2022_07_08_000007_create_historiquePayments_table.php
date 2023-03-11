<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatehistoriquePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historiquePayments', function (Blueprint $table) {
            $table->id();

            $table->double('montant_paiment');
            $table->date('date_payment');

            $table->unsignedBigInteger('id_client')->nullable();
            $table->foreign('id_client')->references('id')->on('clients');

            $table->string('id_commande',100)->nullable();
            $table->foreign('id_commande')->references('id_commande')->on('commandes');
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
        Schema::dropIfExists('historiquePayments');
    }
}
