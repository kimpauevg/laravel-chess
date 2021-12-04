<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Models\ChessGamePiece;
use App\Services\ValueObjects\Collections\CoordinateCollection;

class QueenMoveCalculator extends AbstractChessPieceMoveCalculator
{
    public function calculateMovesForPiece(ChessGamePiece $piece): CoordinateCollection
    {
        $rook_moves = (new RookMoveCalculator($this->game))->calculateMovesForPiece($piece);
        $bishop_moves = (new BishopMoveCalculator($this->game))->calculateMovesForPiece($piece);

        return $rook_moves->merge($bishop_moves);
    }
}
