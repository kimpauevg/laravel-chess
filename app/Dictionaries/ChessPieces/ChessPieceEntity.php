<?php

declare(strict_types=1);

namespace App\Dictionaries\ChessPieces;

use Illuminate\Support\Arr;

class ChessPieceEntity
{
    public string $color;
    public string $name;
    public int $coordinate_x;
    public int $coordinate_y;

    public function __construct(array $attributes)
    {
        $this->color = Arr::get($attributes, 'color');
        $this->name = Arr::get($attributes, 'name');
        $this->coordinate_x = Arr::get($attributes, 'coordinates.x');
        $this->coordinate_y = Arr::get($attributes, 'coordinates.y');
    }
}
