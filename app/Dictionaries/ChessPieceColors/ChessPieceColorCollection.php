<?php

declare(strict_types=1);

namespace App\Dictionaries\ChessPieceColors;

use Illuminate\Support\Collection;

class ChessPieceColorCollection extends Collection
{
    public function getById(string $id): ChessPieceColorEntity
    {
        return $this->firstOrFail('id', $id);
    }
}
