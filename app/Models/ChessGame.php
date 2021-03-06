<?php

declare(strict_types=1);

namespace App\Models;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Models\Builders\ChessGameBuilder;
use App\Models\Collections\ChessGameCollection;
use App\Models\Collections\ChessGamePieceCollection;
use App\Models\Collections\ChessGamePieceMoveCollection;
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
 * @property ChessGamePieceMoveCollection|ChessGamePieceMove[] $moves
 */
class ChessGame extends Model
{
    public const TABLE = 'chess_games';

    protected $table = self::TABLE;

    public function pieces(): HasMany
    {
        return $this->hasMany(ChessGamePiece::class, 'chess_game_id', 'id');
    }

    public function moves(): HasMany
    {
        return $this->hasMany(ChessGamePieceMove::class, 'chess_game_id', 'id');
    }

    public function newEloquentBuilder($query): ChessGameBuilder
    {
        return new ChessGameBuilder($query);
    }

    public function newCollection(array $models = []): ChessGameCollection
    {
        return new ChessGameCollection($models);
    }

    public function getNextMoveChessPieceColor(): string
    {
        $next_move_color = $this->getLastMoveChessPieceColor();

        if ($next_move_color === ChessPieceColorDictionary::LIGHT) {
            return ChessPieceColorDictionary::DARK;
        }

        return ChessPieceColorDictionary::LIGHT;
    }

    public function getLastMoveChessPieceColor(): string
    {
        $last_move = $this->moves->last();

        if (is_null($last_move)) {
            return ChessPieceColorDictionary::DARK;
        }

        return $last_move->color;
    }
}
