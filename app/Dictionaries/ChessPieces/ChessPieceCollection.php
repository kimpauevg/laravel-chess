<?php

declare(strict_types=1);

namespace App\Dictionaries\ChessPieces;

use Illuminate\Support\Collection;

/**
 * @method ChessPieceEntity[] all()
 */
class ChessPieceCollection extends Collection
{
    public function whereCanBePromotedTo(): self
    {
        return $this->whereIn('name', [
            ChessPieceDictionary::QUEEN,
            ChessPieceDictionary::KNIGHT,
            ChessPieceDictionary::ROOK,
            ChessPieceDictionary::BISHOP,
        ]);
    }

    public function getNames(): array
    {
        return $this->pluck('name')->toArray();
    }
}
