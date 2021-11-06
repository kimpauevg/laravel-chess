<?php

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

Route::get('/', [ChessController::class, 'index'])->name('chess-games.index');

Route::group(['prefix' => 'chess-games'], function () {
    Route::get('/{id}', [ChessController::class, 'show'])->name('chess-games.show');
    Route::post('/', [ChessController::class, 'store'])->name('chess-games.store');
});
