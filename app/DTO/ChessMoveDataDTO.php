<?php

declare(strict_types=1);

namespace App\DTO;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;

class ChessMoveDataDTO
{
    public ?string $promotion_to_piece_name = null;

    public function __construct(
        public ChessGame $game,
        public ChessGamePiece $piece,
        public Coordinates $move_coordinates,
    ) {
    }
}
