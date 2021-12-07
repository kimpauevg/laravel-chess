<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterChessGamePieceMoves extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chess_game_piece_moves', function (Blueprint $table) {
            $table->boolean('is_capture')->default(false)->after('coordinate_y');
            $table->boolean('is_en_passant')->default(false)->after('is_capture');
        });

        DB::table('chess_game_piece_moves')
            ->where('type', 2)
            ->update(['is_capture' => true]);

        Schema::table('chess_game_piece_moves', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chess_game_piece_moves', function (Blueprint $table) {
            $table->unsignedTinyInteger('type')->after('chess_piece_name');
        });

        DB::table('chess_game_piece_moves')->update(['type' => 1]);

        DB::table('chess_game_piece_moves')->where('is_capture', true)->update(['type' => 2]);

        Schema::table('chess_game_piece_moves', function (Blueprint $table) {
            $table->dropColumn(['is_capture', 'is_en_passant']);
        });
    }
}
