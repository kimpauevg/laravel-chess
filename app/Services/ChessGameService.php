<?php

declare(strict_types=1);

namespace App\Services;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\Builders\ChessGameBuilder;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ChessGameService
{
    public function getChessGames(): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = $this->getBuilder()->orderBy('created_at')->paginate();

        return $paginator;
    }

    public function getGameById(int $id): ChessGame
    {
        return $this->getBuilder()->findOrFail($id);
    }

    public function storeGame(array $attributes): ChessGame
    {
        $game = new ChessGame();

        $game->name = Arr::get($attributes, 'name');

        $game->save();

        $this->storeDefaultPiecesForGame($game);

        return $game;
    }

    private function storeDefaultPiecesForGame(ChessGame $game): void
    {
        /** @var ChessPieceDictionary $dictionary */
        $dictionary = app(ChessPieceDictionary::class);

        $pieces_collection = $dictionary->getStartingPiecesCollection();

        foreach ($pieces_collection->all() as $piece) {
            $model = new ChessGamePiece();

            $model->chess_game_id = $game->id;
            $model->coordinate_x = $piece->coordinate_x;
            $model->coordinate_y = $piece->coordinate_y;
            $model->name = $piece->name;
            $model->color = $piece->color;

            $model->save();
        }
    }

    private function getBuilder(): ChessGameBuilder
    {
        return app(ChessGameBuilder::class);
    }
}
