<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Builders\ChessGameBuilder;
use App\Models\Collections\ChessGameCollection;
use App\Models\Collections\ChessGamePieceCollection;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property ChessGamePieceCollection|ChessGamePiece[] $pieces
 */
class ChessGame extends Model
{
    public const TABLE = 'chess_games';

    protected string $table = self::TABLE;

    public function newEloquentBuilder($query): ChessGameBuilder
    {
        return new ChessGameBuilder($query);
    }

    public function newCollection(array $models = []): ChessGameCollection
    {
        return new ChessGameCollection($models);
    }

    public function pieces(): HasMany
    {
        return $this->hasMany(ChessGamePiece::class, 'chess_game_id', 'id');
    }
}
