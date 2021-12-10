<?php

declare(strict_types=1);

namespace App\Services;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\Builders\ChessGameBuilder;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\ChessGamePieceMove;
use App\Models\ChessGamePieceMovePromotion;
use App\Models\Collections\ChessGamePieceCollection;
use App\Services\ChessPieceMoveCalculators\PawnMoveCalculator;
use App\ValueObjects\ChessPieceMoves;
use App\ValueObjects\Coordinates;
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
    public function makeMove(int $id, int $chess_piece_id, array $data): void
    {
        $game = $this->getGameById($id);

        $chess_piece = $game->pieces->findOrFail($chess_piece_id);

        $resolver = new ChessPieceMoveResolver($chess_piece, $game);

        $possible_moves = $resolver->getPossibleMoves();

        $coordinates = Arr::get($data, 'coordinates');

        $move_coordinates = new Coordinates(
            (int) Arr::get($coordinates, 'x'),
            (int) Arr::get($coordinates, 'y')
        );

        $move = new ChessGamePieceMove();

        $move->chess_game_id = $game->id;
        $move->move_index = $game->moves->getLastMoveIndex() + 1;
        $move->chess_piece_name = $chess_piece->name;
        $move->previous_coordinate_x = $chess_piece->coordinate_x;
        $move->previous_coordinate_y = $chess_piece->coordinate_y;
        $move->coordinate_x = $move_coordinates->x;
        $move->coordinate_y = $move_coordinates->y;

        $move_is_promotion = $this->shouldBePromoted($chess_piece, $move_coordinates);
        $promotion_to_piece_name = Arr::get($data, 'promotion_to_piece_name');

        if ($move_is_promotion && is_null($promotion_to_piece_name)) {
            throw ValidationException::withMessages([
                'promotion_to_piece_name' => ['Pawn requires promotion']
            ]);
        }

        $move_is_movement = $possible_moves->movement_coordinates_collection
            ->whereCoordinates($move_coordinates->x, $move_coordinates->y)
            ->exists();

        if ($move_is_movement) {
            $chess_piece->coordinate_x = $move_coordinates->x;
            $chess_piece->coordinate_y = $move_coordinates->y;

            $chess_piece->save();

            $move->save();
        }

        $move_is_capture = $possible_moves->capture_coordinates_collection
            ->whereCoordinates($move_coordinates->x, $move_coordinates->y)
            ->exists();

        if ($move_is_capture) {
            $captured_piece = $game->pieces->whereCoordinates($move_coordinates->x, $move_coordinates->y)
                ->firstOrFail();

            $captured_piece->is_captured = true;
            $captured_piece->save();

            $chess_piece->coordinate_x = $move_coordinates->x;
            $chess_piece->coordinate_y = $move_coordinates->y;

            $chess_piece->save();

            $move->is_capture = true;
            $move->save();
        }

        $move_is_en_passant = $possible_moves->en_passant_coordinates_collection
            ->whereCoordinates($move_coordinates->x, $move_coordinates->y)
            ->exists();

        if ($move_is_en_passant) {
            /** @var PawnMoveCalculator $move_calculator */
            $move_calculator = app(PawnMoveCalculator::class);
            $y_modifier = $move_calculator->getYCoordinateModifierForPawn($chess_piece);

            $captured_piece = $game->pieces
                ->whereCoordinates($move_coordinates->x, $move_coordinates->y - $y_modifier)
                ->firstOrFail();

            $captured_piece->is_captured = true;
            $captured_piece->save();

            $chess_piece->coordinate_x = $move_coordinates->x;
            $chess_piece->coordinate_y = $move_coordinates->y;

            $chess_piece->save();

            $move->is_capture = true;
            $move->is_en_passant = true;
            $move->save();
        }

        if ($move_is_promotion) {
            $chess_piece->name = $promotion_to_piece_name;

            $chess_piece->save();

            $promotion_move = new ChessGamePieceMovePromotion();

            $promotion_move->move_id = $move->id;
            $promotion_move->to_name = $promotion_to_piece_name;

            $promotion_move->save();
        }

        if (!($move_is_movement || $move_is_capture || $move_is_en_passant)) {
            throw ValidationException::withMessages([
                'coordinates' => ['Move is not allowed'],
            ]);
        }
    }

    public function shouldBePromoted(ChessGamePiece $piece, Coordinates $new_coordinates): bool
    {
        if ($piece->name !== ChessPieceDictionary::PAWN) {
            return false;
        }

        $dark_pawn_reached_light_starting_position = $piece->color === ChessPieceDictionary::COLOR_DARK
            && $new_coordinates->y === ChessPieceDictionary::LIGHT_PIECE_STARTING_Y_COORDINATE;

        $light_pawn_reached_dark_starting_position = $piece->color === ChessPieceDictionary::COLOR_LIGHT
            && $new_coordinates->y === ChessPieceDictionary::DARK_PIECE_STARTING_Y_COORDINATE;

        if (!($dark_pawn_reached_light_starting_position || $light_pawn_reached_dark_starting_position)) {
            return false;
        }

        return true;
    }

    private function storeDefaultPiecesForGame(ChessGame $game): void
    {
        $pieces_collection = $this->getStartingPiecesCollection();

        foreach ($pieces_collection->all() as $piece) {
            $piece->chess_game_id = $game->id;
            $piece->save();
        }
    }

    private function getStartingPiecesCollection(): ChessGamePieceCollection
    {
        $chess_pieces = [];

        $pieces_by_coordinate_x = [
            1 => ChessPieceDictionary::ROOK,
            2 => ChessPieceDictionary::KNIGHT,
            3 => ChessPieceDictionary::BISHOP,
            4 => ChessPieceDictionary::QUEEN,
            5 => ChessPieceDictionary::KING,
            6 => ChessPieceDictionary::BISHOP,
            7 => ChessPieceDictionary::KNIGHT,
            8 => ChessPieceDictionary::ROOK,
        ];

        for (
            $coordinate_x = ChessPieceDictionary::MIN_COORDINATE_X;
            $coordinate_x <= ChessPieceDictionary::MAX_COORDINATE_X;
            $coordinate_x++
        ) {
            $chess_piece = new ChessGamePiece();
            $chess_piece->coordinate_x = $coordinate_x;
            $chess_piece->name = Arr::get($pieces_by_coordinate_x, $coordinate_x);

            $light_piece = clone $chess_piece;
            $light_piece->coordinate_y = ChessPieceDictionary::LIGHT_PIECE_STARTING_Y_COORDINATE;
            $light_piece->color = ChessPieceDictionary::COLOR_LIGHT;
            $chess_pieces[] = $light_piece;

            $dark_piece = clone $chess_piece;
            $dark_piece->coordinate_y = ChessPieceDictionary::DARK_PIECE_STARTING_Y_COORDINATE;
            $dark_piece->color = ChessPieceDictionary::COLOR_DARK;
            $chess_pieces[] = $dark_piece;

            $pawn = clone $chess_piece;
            $pawn->name = ChessPieceDictionary::PAWN;

            $light_pawn = clone $pawn;
            $light_pawn->color = ChessPieceDictionary::COLOR_LIGHT;
            $light_pawn->coordinate_y = ChessPieceDictionary::LIGHT_PAWN_STARTING_Y_COORDINATE;
            $chess_pieces[] = $light_pawn;

            $dark_pawn = clone $pawn;
            $dark_pawn->color = ChessPieceDictionary::COLOR_DARK;
            $dark_pawn->coordinate_y = ChessPieceDictionary::DARK_PAWN_STARTING_Y_COORDINATE;
            $chess_pieces[] = $dark_pawn;
        }

        return new ChessGamePieceCollection($chess_pieces);
    }

    private function getBuilder(): ChessGameBuilder
    {
        return app(ChessGameBuilder::class);
    }
}
