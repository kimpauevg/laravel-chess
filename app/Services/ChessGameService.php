<?php

declare(strict_types=1);

namespace App\Services;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\Builders\ChessGameBuilder;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\ChessGamePieceMove;
use App\Services\ChessPieceMoveCalculators\PawnMoveCalculator;
use App\ValueObjects\ChessPieceMoves;
use App\ValueObjects\Coordinates;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\ItemNotFoundException;
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

        $possible_moves = $resolver->getPossibleMoves();

        $coordinate_x = (int) Arr::get($coordinates, 'x');
        $coordinate_y = (int) Arr::get($coordinates, 'y');

        $move_coordinates = new Coordinates($coordinate_x, $coordinate_y);

        $move = new ChessGamePieceMove();

        $move->chess_game_id = $game->id;
        $move->move_index = $game->moves->getLastMoveIndex() + 1;
        $move->chess_piece_name = $chess_piece->name;
        $move->previous_coordinate_x = $chess_piece->coordinate_x;
        $move->previous_coordinate_y = $chess_piece->coordinate_y;
        $move->coordinate_x = $move_coordinates->x;
        $move->coordinate_y = $move_coordinates->y;

        $move_is_movement = $possible_moves->movement_coordinates_collection
            ->whereCoordinates($move_coordinates->x, $move_coordinates->y)
            ->exists();

        $move_is_capture = $possible_moves->capture_coordinates_collection
            ->whereCoordinates($move_coordinates->x, $move_coordinates->y)
            ->exists();

        if ($move_is_movement) {
            $chess_piece->coordinate_x = $move_coordinates->x;
            $chess_piece->coordinate_y = $move_coordinates->y;

            $chess_piece->save();

            $move->save();

            return;
        }

        if ($move_is_capture) {
            $captured_piece = $this->getPieceForCapture($game, $chess_piece, $move_coordinates);

            $captured_piece->is_captured = true;
            $captured_piece->save();

            $chess_piece->coordinate_x = $move_coordinates->x;
            $chess_piece->coordinate_y = $move_coordinates->y;

            $chess_piece->save();

            $move->is_capture = true;
            $move->save();
            return;
        }

        throw ValidationException::withMessages([
            'coordinates' => 'Move is not allowed',
        ]);
    }

    private function getPieceForCapture(ChessGame $game, ChessGamePiece $piece, Coordinates $coordinates): ChessGamePiece
    {
        try {
            return $game->pieces->whereCoordinates($coordinates->x, $coordinates->y)->firstOrFail();
        } catch (ItemNotFoundException $exception) {
            if ($piece->name !== ChessPieceDictionary::PAWN) {
                throw $exception;
            }

            // possibly en passante, adjust coordinates
            /** @var PawnMoveCalculator $move_calculator */
            $move_calculator = app(PawnMoveCalculator::class);
            $y_modifier = $move_calculator->getYCoordinateModifierForPawn($piece);

            return $game->pieces->whereCoordinates($coordinates->x, $coordinates->y - $y_modifier)->firstOrFail();
        }
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
