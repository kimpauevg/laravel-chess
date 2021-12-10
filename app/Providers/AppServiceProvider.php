<?php

namespace App\Providers;

use App\Models\Builders\ChessGameBuilder;
use App\Models\Builders\ChessGamePieceBuilder;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(ChessGameBuilder::class, function () {
            return app(ChessGame::class)->newQuery();
        });

        $this->app->bind(ChessGamePieceBuilder::class, function () {
            return app(ChessGamePiece::class)->newQuery();
        });
    }
}
