<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatehistoriqueArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historiqueArticles', function (Blueprint $table) {
            $table->id();
            $table->string('new_nom',150)->nullable();
            $table->string('new_stock')->nullable();

            $table->unsignedBigInteger('id_article')->nullable();
            $table->foreign('id_article')->references('id_article')->on('articles');
            $table->unsignedBigInteger('id_employe')->nullable();
            $table->foreign('id_employe')->references('id')->on('employes');


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
        Schema::dropIfExists('historiqueArticles');
    }
}
