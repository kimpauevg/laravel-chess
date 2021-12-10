<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\ChessPieceMoveCalculators\Traits\MovesOnCoordinatesTrait;
use App\ValueObjects\ChessPieceMoves;
use App\ValueObjects\Coordinates;
use JetBrains\PhpStorm\Pure;

class KnightMoveCalculator extends AbstractChessPieceMoveCalculator
{
    use MovesOnCoordinatesTrait;

    public function calculateMovesForPiece(ChessGamePiece $piece, ChessGame $game): ChessPieceMoves
    {
        $this->setGamePieces($game);

        return $this->getMovesFromCoordinates($this->getCoordinatesForPiece($piece), $piece);
    }

    #[Pure] protected function getCoordinatesForPiece(ChessGamePiece $piece): array
    {
        $x = $piece->coordinate_x;
        $y = $piece->coordinate_y;

        return [
            new Coordinates($x + 1, $y + 2),
            new Coordinates($x + 2, $y + 1),
            new Coordinates($x + 2, $y - 1),
            new Coordinates($x + 1, $y - 2),
            new Coordinates($x - 1, $y - 2),
            new Coordinates($x - 2, $y - 1),
            new Coordinates($x - 2, $y + 1),
            new Coordinates($x - 1, $y + 2),
        ];
    }
}
