<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('nom_article',150);
            $table->string('commentaire')->nullable();
            $table->double('prix_article');
            $table->integer('stock_article');
            $table->string('etat_article',50);

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
        Schema::dropIfExists('articles');
    }
}
