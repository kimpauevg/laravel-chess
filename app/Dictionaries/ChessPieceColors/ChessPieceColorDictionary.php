<?php

declare(strict_types=1);

namespace App\Dictionaries\ChessPieceColors;

class ChessPieceColorDictionary
{
    public const
        DARK = 'dark',
        LIGHT = 'light'
    ;

    public function all(): ChessPieceColorCollection
    {
        $colors = [
            ['id' => self::LIGHT, 'name' => 'Light'],
            ['id' => self::DARK, 'name' => 'Dark'],
        ];

        return new ChessPieceColorCollection(
            collect($colors)->mapInto(ChessPieceColorEntity::class)
        );
    }
}
