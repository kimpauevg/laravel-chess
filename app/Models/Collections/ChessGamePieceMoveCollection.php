<?php

declare(strict_types=1);

namespace App\Models\Collections;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Models\ChessGamePieceMove;

/**
 * @method ChessGamePieceMove|null last(callable $callback = null, $default = null)
 * @method ChessGamePieceMove|null first(callable $callback = null, $default = null)
 */
class ChessGamePieceMoveCollection extends AbstractCollection
{
    public function getLastMoveIndex(): int
    {
        return (int) $this->max('move_index');
    }

    public function whereKing(): static
    {
        return $this->whereName(ChessPieceNameDictionary::KING);
    }

    public function whereRook(): static
    {
        return $this->whereName(ChessPieceNameDictionary::ROOK);
    }

    public function wherePreviousCoordinates(int $coordinate_x, int $coordinate_y): static
    {
        return $this->where('previous_coordinate_x', $coordinate_x)
            ->where('previous_coordinate_y', $coordinate_y);
    }

    public function whereName(string $name): static
    {
        return $this->where('chess_piece_name', $name);
    }

    public function whereColor(string $color): static
    {
        $move_index_modulo = 0;

        if ($color === ChessPieceColorDictionary::LIGHT) {
            $move_index_modulo = 1;
        }

        return $this->filter(fn (ChessGamePieceMove $move) => $move->move_index % 2 === $move_index_modulo);
    }
}
