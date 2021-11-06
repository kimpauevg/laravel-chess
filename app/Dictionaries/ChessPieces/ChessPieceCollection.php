<?php

declare(strict_types=1);

namespace App\Dictionaries\ChessPieces;

use Illuminate\Support\Collection;

/**
 * @method ChessPieceEntity[] all()
 */
class ChessPieceCollection extends Collection
{
    public function whereName(string $name): self
    {
        return $this->where('name', $name);
    }

    public function whereColor(string $color): self
    {
        return $this->where('color', $color);
    }

    public function whereCoordinateY(int $coordinate): self
    {
        return $this->where('coordinate_y', $coordinate);
    }
}
