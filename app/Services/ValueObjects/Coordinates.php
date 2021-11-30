<?php

declare(strict_types=1);

namespace App\Services\ValueObjects;

class Coordinates
{
    public int $x;
    public int $y;

    public function __construct(int $coordinate_x, int $coordinate_y)
    {
        $this->x = $coordinate_x;
        $this->y = $coordinate_y;
    }
}
