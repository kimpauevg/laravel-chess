<?php

declare(strict_types=1);

namespace App\Services\MoveCalculators;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\MoveCalculators\Traits\MovesOnCoordinatesTrait;
use App\DTO\ChessPieceMoves;
use App\DTO\Coordinates;

class KingMoveCalculator extends AbstractChessPieceMoveCalculator
{
    use MovesOnCoordinatesTrait;

    public function calculateMovesForPieceInGame(ChessGamePiece $piece, ChessGame $game): ChessPieceMoves
    {
        $this->setGamePieces($game);

        return $this->getMovesFromCoordinates($this->getCoordinatesForPiece($piece), $piece);
    }

    protected function getCoordinatesForPiece(ChessGamePiece $piece): array
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
