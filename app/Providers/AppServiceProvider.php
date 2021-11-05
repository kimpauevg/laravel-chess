<?php

namespace App\Providers;

use App\Models\Builders\ChessGameBuilder;
use App\Models\ChessGame;
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
            return (new ChessGame())->newQuery();
        });
    }
}
