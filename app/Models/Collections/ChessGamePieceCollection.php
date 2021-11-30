<?php

declare(strict_types=1);

namespace App\Models\Collections;

use App\Models\ChessGamePiece;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method ChessGamePiece|null first(callable $callback = null, $default = null)
 */
class ChessGamePieceCollection extends Collection
{
    public function findOrFail(int $id): ChessGamePiece
    {
        return $this->firstOrFail('id', $id);
    }

    public function whereCoordinateX(int $coordinate): self
    {
        return $this->where('coordinate_x', $coordinate);
    }

    public function whereCoordinateY(int $coordinate): self
    {
        return $this->where('coordinate_y', $coordinate);
    }
}
