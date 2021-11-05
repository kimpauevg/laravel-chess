<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Builders\ChessGameBuilder;
use App\Models\Collections\ChessGameCollection;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ChessGame extends Model
{
    protected string $table = 'chess_games';

    public function newEloquentBuilder($query): ChessGameBuilder
    {
        return new ChessGameBuilder($query);
    }

    public function newCollection(array $models = []): ChessGameCollection
    {
        return new ChessGameCollection($models);
    }
}
