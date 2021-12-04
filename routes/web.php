<?php

use App\Http\Controllers\Ajax;
use App\Http\Controllers\ChessController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::pattern('id', '\d+');
Route::pattern('chess_piece_id', '\d+');

Route::get('/', [ChessController::class, 'index'])->name('chess-games.index');

Route::group(['prefix' => 'chess-games'], function () {
    Route::get('/{id}', [ChessController::class, 'show'])->name('chess-games.show');
    Route::post('/', [ChessController::class, 'store'])->name('chess-games.store');

    Route::post('/{id}/piece/{chess_piece_id}/move', [ChessController::class, 'movePiece'])
        ->name('chess-games.move-chess-piece');

    Route::group(['prefix' => 'ajax'], function () {
        Route::get('/{id}/piece/{chess_piece_id}/moves', [Ajax\ChessController::class, 'getChessPieceMoves'])
            ->name('chess-games.ajax.chess-piece-moves');
    });
});
