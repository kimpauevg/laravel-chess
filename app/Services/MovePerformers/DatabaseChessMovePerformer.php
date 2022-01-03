<?php

declare(strict_types=1);

namespace App\Services\MovePerformers;

use App\Models\ChessGamePiece;
use App\Models\ChessGamePieceMove;
use App\Models\ChessGamePieceMovePromotion;

class DatabaseChessMovePerformer extends AbstractChessPieceMovePerformer
{
    protected function saveMove(ChessGamePieceMove $move): void
    {
        $move->save();
    }

    protected function saveChessPiece(ChessGamePiece $piece): void
    {
        $piece->save();
    }

    protected function savePromotionMove(ChessGamePieceMovePromotion $promotion_move): void
    {
        $promotion_move->save();
    }
}
