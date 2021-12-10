<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Builders\ChessGamePieceMoveBuilder;
use App\Models\Collections\ChessGamePieceMoveCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $chess_game_id
 * @property int $move_index
 * @property string $chess_piece_name
 * @property int $type
 * @property int $previous_coordinate_x
 * @property int $previous_coordinate_y
 * @property int $coordinate_x
 * @property int $coordinate_y
 * @property bool $is_capture
 * @property bool $is_en_passant
 * @property bool $is_check
 * @property bool $is_mate
 */
class ChessGamePieceMove extends Model
{
    public const TABLE = 'chess_game_piece_moves';
    protected string $table = self::TABLE;

    protected array $casts = [
        'chess_game_id'         => 'int',
        'type'                  => 'int',
        'move_index'            => 'int',
        'previous_coordinate_x' => 'int',
        'previous_coordinate_y' => 'int',
        'coordinate_x'          => 'int',
        'coordinate_y'          => 'int',
        'is_check'              => 'bool',
        'is_mate'               => 'bool',
        'is_capture'            => 'bool',
        'is_en_passant'         => 'bool',
    ];

    public function promotion(): HasOne
    {
        return $this->hasOne(ChessGamePieceMovePromotion::class, 'move_id', 'id');
    }

    public function newEloquentBuilder($query): ChessGamePieceMoveBuilder
    {
        return new ChessGamePieceMoveBuilder($query);
    }

    public function newCollection(array $models = []): ChessGamePieceMoveCollection
    {
        return new ChessGamePieceMoveCollection($models);
    }
}
