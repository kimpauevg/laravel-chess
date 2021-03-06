<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Builders\ChessGamePieceMovePromotionBuilder;
use App\Models\Collections\ChessGamePieceMovePromotionCollection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $move_id
 * @property string $to_name
 */
class ChessGamePieceMovePromotion extends Model
{
    public const TABLE = 'chess_game_piece_move_promotions';
    protected $table = self::TABLE;

    public $timestamps = false;

    public function newEloquentBuilder($query): ChessGamePieceMovePromotionBuilder
    {
        return new ChessGamePieceMovePromotionBuilder($query);
    }

    /** @codeCoverageIgnore */
    public function newCollection(array $models = []): ChessGamePieceMovePromotionCollection
    {
        return new ChessGamePieceMovePromotionCollection($models);
    }
}
