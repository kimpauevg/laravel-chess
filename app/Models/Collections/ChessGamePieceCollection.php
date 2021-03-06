<?php

declare(strict_types=1);

namespace App\Models\Collections;

use App\DTO\Coordinates;
use App\Models\ChessGamePiece;

/**
 * @method ChessGamePiece|null first(callable $callback = null, $default = null)
 * @method ChessGamePiece firstOrFail($key = null, $operator = null, $value = null)
 * @method ChessGamePiece[] all()
 */
class ChessGamePieceCollection extends AbstractCollection
{
    public function findOrFail(int $id): ChessGamePiece
    {
        return $this->firstOrFail('id', $id);
    }

    public function whereCoordinates(int $x, int $y): static
    {
        return $this->whereCoordinateX($x)->whereCoordinateY($y);
    }

    public function whereCoordinatesDTO(Coordinates $coordinates): static
    {
        return $this->whereCoordinates($coordinates->x, $coordinates->y);
    }

    public function whereCoordinateX(int $coordinate): static
    {
        return $this->where('coordinate_x', $coordinate);
    }

    public function whereCoordinateXBetween(int $from, int $to): static
    {
        return $this->where('coordinate_x', '>', $from)
            ->where('coordinate_x', '<', $to);
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
