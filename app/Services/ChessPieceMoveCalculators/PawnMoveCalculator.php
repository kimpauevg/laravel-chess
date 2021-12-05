<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\ValueObjects\Collections\CoordinatesCollection;
use App\Services\ValueObjects\CoordinateModifiers;
use App\Services\ValueObjects\Coordinates;

class PawnMoveCalculator extends AbstractChessPieceMoveCalculator
{
    public const LIGHT_PAWN_STARTING_Y_COORDINATE = 2;
    public const DARK_PAWN_STARTING_Y_COORDINATE = 7;

    public function calculateMovesForPiece(ChessGamePiece $piece, ChessGame $game): CoordinatesCollection
    {
        $this->setGamePieces($game);

        // Light pieces move upward
        $modifier = CoordinateModifiers::MODIFIER_ADD;

        if ($piece->color === ChessPieceDictionary::COLOR_DARK) {
            $modifier = CoordinateModifiers::MODIFIER_SUBTRACT;
        }

        return $this->getPawnMovementsUsingModifier($piece, $modifier);
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
            && $piece->coordinate_y === self::LIGHT_PAWN_STARTING_Y_COORDINATE;

        $is_dark_piece_starting_point = $piece->color === ChessPieceDictionary::COLOR_DARK
            && $piece->coordinate_y === self::DARK_PAWN_STARTING_Y_COORDINATE;

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
