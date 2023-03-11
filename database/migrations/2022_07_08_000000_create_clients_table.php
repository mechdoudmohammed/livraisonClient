<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nom',50)->nullable();
            $table->string('prenom',50)->nullable();
            $table->string('telephone',20);
            $table->string('cin',20)->nullable();
            $table->string('cin_photo')->nullable();
            $table->string('adresse')->nullable();
            $table->string('company',100)->nullable();
            $table->string('website',100)->nullable();
            $table->string('ribBank',50)->nullable();
            $table->boolean('stock')->default(0);
            $table->string('username',100)->unique();

            $table->string('ice_client',50)->nullable();
            $table->string('rc_client',50)->nullable();
            $table->string('if_client',50)->nullable();
            $table->boolean('notification_statut')->default(1);

            $table->string('role',20)->default('Client');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('statut',20)->default('Inactive');
            $table->unsignedBigInteger('id_ville')->nullable();
            $table->foreign('id_ville')->references('id')->on('villes');
            $table->unsignedBigInteger('id_secteur')->nullable();
            $table->foreign('id_secteur')->references('id')->on('secteurs');
            $table->unsignedBigInteger('ajoute_par')->nullable();
            $table->foreign('ajoute_par')->references('id')->on('employes');        
            $table->unsignedBigInteger('id_bank')->nullable();
            $table->foreign('id_bank')->references('id')->on('banques');      
            $table->unsignedBigInteger('superviseur')->nullable();
            $table->foreign('superviseur')->references('id')->on('clients');

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
        Schema::dropIfExists('clients');
    }
}
