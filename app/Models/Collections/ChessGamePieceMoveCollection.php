<?php

declare(strict_types=1);

namespace App\Models\Collections;

use Illuminate\Database\Eloquent\Collection;

class ChessGamePieceMoveCollection extends Collection
{
    public function getLastMoveIndex(): int
    {
        return (int) $this->max('move_index');
    }
}
