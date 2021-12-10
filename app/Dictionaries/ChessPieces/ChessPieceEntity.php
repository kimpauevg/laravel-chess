<?php

declare(strict_types=1);

namespace App\Dictionaries\ChessPieces;

use Illuminate\Support\Arr;

class ChessPieceEntity
{
    public string $name;
    public string $title;

    public function __construct(array $attributes = [])
    {
        $this->name = Arr::get($attributes, 'name');
        $this->title = Arr::get($attributes, 'title');
    }
}
