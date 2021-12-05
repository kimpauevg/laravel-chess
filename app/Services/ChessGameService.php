<?php

declare(strict_types=1);

namespace App\Services;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\Builders\ChessGameBuilder;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\ValueObjects\ChessPieceMoves;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class ChessGameService
{
    public function getChessGames(): LengthAwarePaginator
    {
        return $this->getBuilder()->orderBy('created_at')->paginate();
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

    public function getPossibleMovesForChessPieceById(int $game_id, int $chess_piece_id): ChessPieceMoves
    {
        $game = $this->getGameById($game_id);

        $chess_piece = $game->pieces->findOrFail($chess_piece_id);

        $resolver = new ChessPieceMoveResolver($chess_piece, $game);

        return $resolver->getPossibleMoves();
    }

    /**
     * @throws ValidationException
     */
    public function makeMove(int $id, int $chess_piece_id, array $coordinates): void
    {
        $game = $this->getGameById($id);

        $chess_piece = $game->pieces->findOrFail($chess_piece_id);

        $resolver = new ChessPieceMoveResolver($chess_piece, $game);

        $moves = $resolver->getPossibleMoves();

        $coordinate_x = (int) Arr::get($coordinates, 'x');
        $coordinate_y = (int) Arr::get($coordinates, 'y');

        $move_is_movement = $moves->movement_coordinates_collection
            ->whereCoordinates($coordinate_x, $coordinate_y)
            ->exists();

        $move_is_capture = $moves->capture_coordinates_collection
            ->whereCoordinates($coordinate_x, $coordinate_y)
            ->exists();

        if ($move_is_movement) {
            $chess_piece->coordinate_x = $coordinate_x;
            $chess_piece->coordinate_y = $coordinate_y;

            $chess_piece->save();

            return;
        }

        if ($move_is_capture) {
            $captured_piece = $game->pieces->whereCoordinates($coordinate_x, $coordinate_y)->firstOrFail();

            $captured_piece->is_captured = true;
            $captured_piece->save();

            $chess_piece->coordinate_x = $coordinate_x;
            $chess_piece->coordinate_y = $coordinate_y;

            $chess_piece->save();
            return;
        }

        throw ValidationException::withMessages([
            'coordinates' => 'Move is not allowed',
        ]);
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
