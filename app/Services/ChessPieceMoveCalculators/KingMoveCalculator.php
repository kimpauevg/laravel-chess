<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Models\ChessGamePiece;
use App\Services\ChessPieceMoveCalculators\Traits\MovesOnCoordinatesTrait;
use App\Services\ValueObjects\Coordinates;
use JetBrains\PhpStorm\Pure;

class KingMoveCalculator extends AbstractChessPieceMoveCalculator
{
    use MovesOnCoordinatesTrait;

    #[Pure] protected function getCoordinatesForPiece(ChessGamePiece $piece): array
    {
        $x = $piece->coordinate_x;
        $y = $piece->coordinate_y;

        return [
            new Coordinates($x, $y + 1),
            new Coordinates($x + 1, $y + 1),
            new Coordinates($x + 1, $y),
            new Coordinates($x + 1, $y - 1),
            new Coordinates($x, $y - 1),
            new Coordinates($x - 1, $y - 1),
            new Coordinates($x - 1, $y),
            new Coordinates($x - 1, $y + 1),
        ];
    }
}
