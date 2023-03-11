<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatemsgSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('msgSettings', function (Blueprint $table) {
            $table->id();

            $table->string('api_key')->nullable();
            $table->string('type')->nullable();
            $table->string('mode')->nullable();
            $table->string('device')->nullable();
            $table->string('sim')->nullable();
            $table->string('priority')->nullable();
            $table->integer('account')->nullable();

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
        Schema::dropIfExists('msgSettings');


    }
}
