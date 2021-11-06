<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChessGamePieces extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chess_game_pieces', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chess_game_id')->references('id')->on('chess_games')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('name');
            $table->string('color');
            $table->tinyInteger('coordinate_x')->unsigned();
            $table->tinyInteger('coordinate_y')->unsigned();

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
        Schema::dropIfExists('chess_game_pieces');
    }
}
