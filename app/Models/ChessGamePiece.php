<?php

declare(strict_types=1);

namespace App\Models;

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
}
