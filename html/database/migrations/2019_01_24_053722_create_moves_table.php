<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moves', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('player_id');
            $table->integer('game_id');
            $table->decimal('step');
            $table->integer('score');
            $table->timestamps();

            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade')->onUpdate('restrict');
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moves');
    }
}
