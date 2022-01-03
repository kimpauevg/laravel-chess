<?php

declare(strict_types=1);

namespace App\Models\Collections;

use App\Models\ChessGamePiece;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method ChessGamePiece|null first(callable $callback = null, $default = null)
 * @method ChessGamePiece firstOrFail($key = null, $operator = null, $value = null)
 */
class ChessGamePieceCollection extends Collection
{
    public function findOrFail(int $id): ChessGamePiece
    {
        return $this->firstOrFail('id', $id);
    }

    public function whereCoordinates(int $x, int $y): static
    {
        return $this->whereCoordinateX($x)->whereCoordinateY($y);
    }

    public function whereCoordinateX(int $coordinate): static
    {
        return $this->where('coordinate_x', $coordinate);
    }

    public function whereCoordinateY(int $coordinate): static
    {
        return $this->where('coordinate_y', $coordinate);
    }

    public function whereName(string $name): static
    {
        return $this->where('name', $name);
    }

    public function whereColor(string $color): static
    {
        return $this->where('color', $color);
    }
}
