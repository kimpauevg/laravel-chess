<?php

declare(strict_types=1);

namespace App\DTO\Collections;

use App\DTO\Coordinates;
use Illuminate\Support\Collection;

/**
 * @method Coordinates[] all()
 * @method Coordinates|null first(callable $callback = null, $default = null)
 */
class CoordinatesCollection extends Collection
{
    public function subtractCollection(CoordinatesCollection $collection): static
    {
        return $this->filter(function (Coordinates $coordinates) use ($collection) {
            return $collection->whereX($coordinates->x)->whereY($coordinates->y)->count() === 0;
        });
    }

    public function whereX(int $coordinate): static
    {
        return $this->where('x', $coordinate);
    }

    public function whereY(int $coordinate): static
    {
        return $this->where('y', $coordinate);
    }

    public function whereCoordinates(int $x, int $y): static
    {
        return $this->whereX($x)->whereY($y);
    }

    public function whereCoordinatesDTO(Coordinates $coordinates): static
    {
        return $this->whereCoordinates($coordinates->x, $coordinates->y);
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }
}
