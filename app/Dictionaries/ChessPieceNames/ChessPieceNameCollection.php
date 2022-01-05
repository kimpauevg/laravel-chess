<?php

declare(strict_types=1);

namespace App\Dictionaries\ChessPieceNames;

use Illuminate\Support\Collection;

/**
 * @method ChessPieceNameEntity[] all()
 */
class ChessPieceNameCollection extends Collection
{
    public function whereCanBePromotedTo(): static
    {
        return $this->whereIn('name', [
            ChessPieceNameDictionary::QUEEN,
            ChessPieceNameDictionary::KNIGHT,
            ChessPieceNameDictionary::ROOK,
            ChessPieceNameDictionary::BISHOP,
        ]);
    }

    public function getNames(): array
    {
        return $this->pluck('name')->toArray();
    }

    public function getByName(string $name): ChessPieceNameEntity
    {
        return $this->where('name', $name)->firstOrFail();
    }
}
