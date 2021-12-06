<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChessGamePieceMoves extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chess_game_piece_moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chess_game_id')
                ->references('id')
                ->on('chess_games')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->smallInteger('move_index');

            $table->string('chess_piece_name');
            $table->unsignedTinyInteger('type');

            $table->tinyInteger('previous_coordinate_x');
            $table->tinyInteger('previous_coordinate_y');

            $table->tinyInteger('coordinate_x');
            $table->tinyInteger('coordinate_y');

            $table->boolean('is_check')->default(false);
            $table->boolean('is_mate')->default(false);

            $table->timestamps();
        });

        Schema::create('chess_game_piece_move_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('move_id')
                ->references('id')
                ->on('chess_game_piece_moves')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('to_name');
        });
    }

    /**
     *
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chess_game_piece_move_promotions');
        Schema::dropIfExists('chess_game_piece_moves');
    }
}
