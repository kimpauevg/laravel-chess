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
 * @property int $coordinate_x
 * @property int $coordinate_y
 */
class ChessGamePiece extends Model
{
    public const TABLE = 'chess_game_pieces';
    protected string $table = self::TABLE;

    public function newCollection(array $models = []): ChessGamePieceCollection
    {
        return new ChessGamePieceCollection($models);
    }

    public function newEloquentBuilder($query): ChessGamePieceBuilder
    {
        return new ChessGamePieceBuilder($query);
    }
}
