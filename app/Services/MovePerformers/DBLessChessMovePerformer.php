<?php

declare(strict_types=1);

namespace App\Services\MovePerformers;

use App\Models\ChessGamePiece;
use App\Models\ChessGamePieceMove;
use App\Models\ChessGamePieceMovePromotion;

class DBLessChessMovePerformer extends AbstractChessPieceMovePerformer
{
    protected function saveMove(ChessGamePieceMove $move): void
    {
    }

    protected function saveChessPiece(ChessGamePiece $piece): void
    {
    }

    protected function savePromotionMove(ChessGamePieceMovePromotion $promotion_move): void
    {
    }
}
