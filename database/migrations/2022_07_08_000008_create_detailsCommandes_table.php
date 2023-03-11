<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatedetailsCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detailsCommandes', function (Blueprint $table) {
            $table->id();
            $table->integer('quantite_article')->nullable();
            $table->unsignedBigInteger('id_article')->nullable();
            $table->string('id_commande',100)->nullable();

            $table->foreign('id_article')->references('id')->on('articles')->onDelete('cascade');

            $table->foreign('id_commande')->references('id_commande')->on('commandes')->onDelete('cascade');

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
        Schema::dropIfExists('detailsCommandes');
    }
}
