<?php

declare(strict_types=1);

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class ChessGamePieceBuilder extends Builder
{
    public const SCOPE_NOT_CAPTURED = 'not_captured';

    public function whereNotCaptured(): self
    {
        return $this->where('is_captured', false);
    }
}
