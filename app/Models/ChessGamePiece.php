<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Builders\ChessGamePieceBuilder;
use App\Models\Collections\ChessGamePieceCollection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $chess_game_id
 * @property string $color
 * @property string $name
 * @property bool $is_captured
 * @property int $coordinate_x
 * @property int $coordinate_y
 */
class ChessGamePiece extends Model
{
    public const TABLE = 'chess_game_pieces';
    protected string $table = self::TABLE;

    protected array $casts = [
        'chess_game_id' => 'int',
        'is_captured'   => 'bool',
        'coordinate_x'  => 'int',
        'coordinate_y'  => 'int',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(ChessGamePieceBuilder::SCOPE_NOT_CAPTURED, function (ChessGamePieceBuilder $builder) {
            $builder->whereNotCaptured();
        });
    }

    public function newCollection(array $models = []): ChessGamePieceCollection
    {
        return new ChessGamePieceCollection($models);
    }

    public function newEloquentBuilder($query): ChessGamePieceBuilder
    {
        return new ChessGamePieceBuilder($query);
    }
}
