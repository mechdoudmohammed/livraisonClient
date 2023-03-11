<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatestoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('nom_store',120);
            $table->string('siteweb_store',80);
            $table->string('telephone_store',20);
            $table->string('adresse_store');
            $table->string('statut_store',20)->default('Inactive');
            $table->boolean('favorite_store')->default(false);
            $table->unsignedBigInteger('id_ville');
            $table->foreign('id_ville')->references('id')->on('villes');
            
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
        Schema::dropIfExists('stores');
    }
}
