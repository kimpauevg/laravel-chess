<?php

declare(strict_types=1);

namespace App\Models\Builders;

use App\Models\ChessGame;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method ChessGame findOrFail($id, $columns = ['*'])
 */
class ChessGameBuilder extends Builder
{
}
