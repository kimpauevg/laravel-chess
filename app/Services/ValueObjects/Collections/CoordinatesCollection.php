<?php

declare(strict_types=1);

namespace App\Services\ValueObjects\Collections;

use App\Services\ValueObjects\Coordinates;
use Illuminate\Support\Collection;

/**
 * @method Coordinates[] all()
 */
class CoordinatesCollection extends Collection
{
    public function subtractCollection(CoordinatesCollection $collection): self
    {
        return $this->filter(function (Coordinates $coordinates) use ($collection) {
            return $collection->whereX($coordinates->x)->whereY($coordinates->y)->count() === 0;
        });
    }

    public function whereX(int $coordinate): self
    {
        return $this->where('x', $coordinate);
    }

    public function whereY(int $coordinate): self
    {
        return $this->where('y', $coordinate);
    }
}
