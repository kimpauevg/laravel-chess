<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\ChessPieceMoveCalculators\Traits\ChecksCoordinatesTrait;
use App\Services\ValueObjects\ChessPieceMoves;

abstract class AbstractChessPieceMoveCalculator
{
    use ChecksCoordinatesTrait;

    abstract public function calculateMovesForPiece(ChessGamePiece $piece, ChessGame $game): ChessPieceMoves;
}
