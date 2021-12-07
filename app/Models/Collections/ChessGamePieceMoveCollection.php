<?php

declare(strict_types=1);

namespace App\Models\Collections;

use App\Models\ChessGamePieceMove;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method ChessGamePieceMove|null last(callable $callback = null, $default = null)
 */
class ChessGamePieceMoveCollection extends Collection
{
    public function getLastMoveIndex(): int
    {
        return (int) $this->max('move_index');
    }
}
