<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\ValueObjects\Collections\CoordinateCollection;
use App\Services\ValueObjects\Coordinates;

class KingMoveCalculator extends AbstractChessPieceMoveCalculator
{
    public function calculateMovesForPiece(ChessGamePiece $piece, ChessGame $game): CoordinateCollection
    {
        $this->setGamePieces($game);

        $x = $piece->coordinate_x;
        $y = $piece->coordinate_y;

        $possible_coordinates = [
            new Coordinates($x, $y + 1),
            new Coordinates($x + 1, $y + 1),
            new Coordinates($x + 1, $y),
            new Coordinates($x + 1, $y - 1),
            new Coordinates($x, $y - 1),
            new Coordinates($x - 1, $y - 1),
            new Coordinates($x - 1, $y),
            new Coordinates($x - 1, $y + 1),
        ];

        return $this->getFromPossibleCoordinates($possible_coordinates);
    }
}
