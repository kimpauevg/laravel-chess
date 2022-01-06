<?php

declare(strict_types=1);

namespace App\Dictionaries\ChessPieceColors;

use Illuminate\Support\Arr;

class ChessPieceColorEntity
{
    public string $id;
    public string $name;

    public function __construct(array $attributes = [])
    {
        $this->id = Arr::get($attributes, 'id');
        $this->name = Arr::get($attributes, 'name');
    }
}
