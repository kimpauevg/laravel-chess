<?php

declare(strict_types=1);

namespace App\Services\MoveCalculators;

use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Models\ChessGamePiece;

class ChessPieceMoveCalculatorFactory
{
    public function __construct(
        private RookMoveCalculator $rook_move_calculator,
        private BishopMoveCalculator $bishop_move_calculator,
        private QueenMoveCalculator $queen_move_calculator,
        private PawnMoveCalculator $pawn_move_calculator,
        private KingMoveCalculator $king_move_calculator,
        private KnightMoveCalculator $knight_move_calculator,
    ) {
    }

    public function make(ChessGamePiece $piece): AbstractChessPieceMoveCalculator
    {
        return match ($piece->name) {
            ChessPieceNameDictionary::ROOK   => $this->rook_move_calculator,
            ChessPieceNameDictionary::BISHOP => $this->bishop_move_calculator,
            ChessPieceNameDictionary::QUEEN  => $this->queen_move_calculator,
            ChessPieceNameDictionary::PAWN   => $this->pawn_move_calculator,
            ChessPieceNameDictionary::KNIGHT => $this->knight_move_calculator,
            ChessPieceNameDictionary::KING   => $this->king_move_calculator,
        };
    }
}
