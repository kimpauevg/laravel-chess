<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators\Traits;

use App\Models\ChessGamePiece;
use App\ValueObjects\ChessPieceMoves;

trait MovesOnCoordinatesTrait
{
    use ChecksCoordinatesTrait;

    protected function getMovesFromCoordinates(array $possible_coordinates, ChessGamePiece $piece): ChessPieceMoves
    {
        $valid_coordinates = [];

        foreach ($possible_coordinates as $coordinates) {
            if ($this->isCoordinateInvalid($coordinates)) {
                continue;
            }

            $valid_coordinates[] = $coordinates;
        }

        $moves = new ChessPieceMoves();

        foreach ($valid_coordinates as $coordinates) {
            $piece_on_coordinates = $this->getChessPieceWithCoordinates($coordinates);
            $piece_on_coordinates_exists = !is_null($piece_on_coordinates);

            if ($piece_on_coordinates_exists && $piece_on_coordinates->color != $piece->color) {
                $moves->capture_coordinates_collection->add($coordinates);
            }

            if ($piece_on_coordinates_exists) {
                continue;
            }

            $moves->movement_coordinates_collection->add($coordinates);
        }

        return $moves;
    }
}
