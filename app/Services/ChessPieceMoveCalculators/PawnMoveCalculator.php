<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\ChessGamePieceMove;
use App\Services\ChessPieceMoveCalculators\Traits\MovesOnCoordinatesTrait;
use App\ValueObjects\ChessPieceMoves;
use App\ValueObjects\Collections\CoordinatesCollection;
use App\ValueObjects\CoordinateModifiers;
use App\ValueObjects\Coordinates;

class PawnMoveCalculator extends AbstractChessPieceMoveCalculator
{
    use MovesOnCoordinatesTrait;

    public function calculateMovesForPiece(ChessGamePiece $piece, ChessGame $game): ChessPieceMoves
    {
        $this->setGamePieces($game);

        $y_coordinate_modifier = $this->getYCoordinateModifierForPawn($piece);

        $moves = new ChessPieceMoves();
        $moves->movement_coordinates_collection = $this->getPawnMovementsUsingModifier($piece, $y_coordinate_modifier);
        $moves->capture_coordinates_collection = $this->getPawnCapturesUsingModifier($piece, $y_coordinate_modifier);
        $moves->en_passant_coordinates_collection = $this->getEnPassant($piece, $game);

        return $moves;
    }

    private function getPawnCapturesUsingModifier(
        ChessGamePiece $piece,
        int $y_coordinate_modifier,
    ): CoordinatesCollection {
        $new_y_coordinate = $piece->coordinate_y + $y_coordinate_modifier;

        $coordinates = [
            new Coordinates($piece->coordinate_x + 1, $new_y_coordinate),
            new Coordinates($piece->coordinate_x - 1, $new_y_coordinate),
        ];

        $moves_on_coordinates = $this->getMovesFromCoordinates($coordinates, $piece);

        return $moves_on_coordinates->capture_coordinates_collection;
    }

    /**
     * @param ChessGamePiece $piece
     * @param ChessGame $game
     * @return CoordinatesCollection
     */
    private function getEnPassant(ChessGamePiece $piece, ChessGame $game): CoordinatesCollection
    {
        $last_move = $game->moves->last();

        $coordinates_collection = new CoordinatesCollection();

        if (!$this->isLastMoveValidForEnPassant($last_move)) {
            return $coordinates_collection;
        }

        $pawns_are_on_adjacent_lines = abs($piece->coordinate_x - $last_move->coordinate_x) === 1;

        if ($piece->coordinate_y === $last_move->coordinate_y
            && $pawns_are_on_adjacent_lines
        ) {
            $en_passant_y_coordinate = $piece->coordinate_y + $this->getYCoordinateModifierForPawn($piece);
            $coordinates_collection->add(new Coordinates($last_move->coordinate_x, $en_passant_y_coordinate));
        }

        return $coordinates_collection;
    }

    public function getYCoordinateModifierForPawn(ChessGamePiece $piece): int
    {
        if ($piece->color === ChessPieceDictionary::COLOR_DARK) {
            return CoordinateModifiers::MODIFIER_SUBTRACT;
        }

        return CoordinateModifiers::MODIFIER_ADD;
    }

    private function isLastMoveValidForEnPassant(?ChessGamePieceMove $last_move): bool
    {
        if (is_null($last_move)) {
            return false;
        }

        $pawn_made_two_square_movement = $last_move->chess_piece_name === ChessPieceDictionary::PAWN
            && abs($last_move->coordinate_y - $last_move->previous_coordinate_y) === 2;

        if (!$pawn_made_two_square_movement) {
            return false;
        }

        return true;
    }

    private function getPawnMovementsUsingModifier(
        ChessGamePiece $piece,
        int $y_coordinate_modifier
    ): CoordinatesCollection {
        $movement_collection = new CoordinatesCollection();

        $coordinate_before_pawn = new Coordinates(
            $piece->coordinate_x,
            $piece->coordinate_y + $y_coordinate_modifier
        );

        if (!$this->canMoveToCoordinate($coordinate_before_pawn)) {
            return $movement_collection;
        }

        $movement_collection->add($coordinate_before_pawn);

        $two_square_move_from_start_coordinates = new Coordinates(
            $piece->coordinate_x,
            $piece->coordinate_y + ($y_coordinate_modifier * 2)
        );

        $is_light_piece_at_starting_point = $piece->color === ChessPieceDictionary::COLOR_LIGHT
            && $piece->coordinate_y === ChessPieceDictionary::LIGHT_PAWN_STARTING_Y_COORDINATE;

        $is_dark_piece_starting_point = $piece->color === ChessPieceDictionary::COLOR_DARK
            && $piece->coordinate_y === ChessPieceDictionary::DARK_PAWN_STARTING_Y_COORDINATE;

        if (
            ($is_light_piece_at_starting_point || $is_dark_piece_starting_point)
            && $this->canMoveToCoordinate($two_square_move_from_start_coordinates)
        ) {
            $movement_collection->add($two_square_move_from_start_coordinates);
        }

        return $movement_collection;
    }

    private function canMoveToCoordinate(Coordinates $coordinates): bool
    {
        if ($this->isCoordinateInvalid($coordinates)) {
            return false;
        }

        if ($this->isGridWithCoordinatesTaken($coordinates)) {
            return false;
        }

        return true;
    }
}
