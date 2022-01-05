<?php

declare(strict_types=1);

namespace App\Services\MoveCalculators;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\DTO\ChessPieceMoves;

class QueenMoveCalculator extends AbstractChessPieceMoveCalculator
{
    public function __construct(
        private RookMoveCalculator $rook_move_calculator,
        private BishopMoveCalculator $bishop_move_calculator,
    ) {
    }

    public function calculateMovesForPieceInGame(ChessGamePiece $piece, ChessGame $game): ChessPieceMoves
    {
        $rook_moves = $this->rook_move_calculator->calculateMovesForPieceInGame($piece, $game);

        $bishop_moves = $this->bishop_move_calculator->calculateMovesForPieceInGame($piece, $game);

        $queen_moves = new ChessPieceMoves();
        $queen_moves->movement_coordinates_collection = $rook_moves->movement_coordinates_collection
            ->merge($bishop_moves->movement_coordinates_collection);
        $queen_moves->capture_coordinates_collection = $rook_moves->capture_coordinates_collection
            ->merge($bishop_moves->capture_coordinates_collection);

        return $queen_moves;
    }
}
