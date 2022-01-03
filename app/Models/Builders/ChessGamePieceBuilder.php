<?php

declare(strict_types=1);

namespace App\Models\Builders;

use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGamePieceCollection;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method ChessGamePieceCollection|ChessGamePiece[] get($columns = ['*'])
 */
class ChessGamePieceBuilder extends Builder
{
    public const SCOPE_NOT_CAPTURED = 'not_captured';

    public function whereNotCaptured(): static
    {
        return $this->where('is_captured', false);
    }
}
