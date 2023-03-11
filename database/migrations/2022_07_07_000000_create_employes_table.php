<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateEmployesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->string('nom',50);
            $table->string('prenom',50);
            $table->string('telephone',20);
            $table->string('cin',20)->nullable();
            $table->string('cin_photo')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ribBank',50)->nullable();
            $table->string('username',50)->nullable()->unique();
            $table->double('pourcentage_ramassage')->nullable();
            $table->double('prix_ramassage')->nullable();

            $table->string('role',20)->default('Employe');
            $table->string('email',100)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('statut')->default('Inactive');
            $table->unsignedBigInteger('id_ville')->nullable();
            $table->foreign('id_ville')->references('id')->on('villes');
            $table->unsignedBigInteger('id_zone')->nullable();
            $table->foreign('id_zone')->references('id')->on('zones');
            $table->unsignedBigInteger('superviseur')->nullable();
            $table->foreign('superviseur')->references('id')->on('employes');
            $table->unsignedBigInteger('ajoute_par')->nullable();
            $table->foreign('ajoute_par')->references('id')->on('employes');
            $table->unsignedBigInteger('id_bank')->nullable();
            $table->foreign('id_bank')->references('id')->on('banques');     

            $table->rememberToken();
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
        Schema::dropIfExists('employes');
    }
}
