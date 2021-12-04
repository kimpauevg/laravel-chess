<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\ValueObjects\Collections\CoordinateCollection;

class QueenMoveCalculator extends AbstractChessPieceMoveCalculator
{
    public function __construct(
        private RookMoveCalculator $rook_move_calculator,
        private BishopMoveCalculator $bishop_move_calculator,
    ) {
    }

    public function calculateMovesForPiece(ChessGamePiece $piece, ChessGame $game): CoordinateCollection
    {
        $rook_moves = $this->rook_move_calculator->calculateMovesForPiece($piece, $game);
        $bishop_moves = $this->bishop_move_calculator->calculateMovesForPiece($piece, $game);

        return $rook_moves->merge($bishop_moves);
    }
}
