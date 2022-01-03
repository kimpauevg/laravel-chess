<?php

declare(strict_types=1);

namespace App\Services\MoveCalculators;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\MoveCalculators\Traits\ChecksCoordinatesTrait;
use App\DTO\ChessPieceMoves;

abstract class AbstractChessPieceMoveCalculator
{
    use ChecksCoordinatesTrait;

    abstract public function calculateMovesForPieceInGame(ChessGamePiece $piece, ChessGame $game): ChessPieceMoves;
}
