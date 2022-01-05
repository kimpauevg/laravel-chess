<?php

declare(strict_types=1);

namespace App\Dictionaries\ChessPieceNames;

class ChessPieceNameDictionary
{
    public const
        PAWN = 'pawn',
        BISHOP = 'bishop',
        KNIGHT = 'knight',
        ROOK = 'rook',
        QUEEN = 'queen',
        KING = 'king'
    ;

    public function all(): ChessPieceNameCollection
    {
        $pieces = [
            ['name' => self::PAWN, 'title' => 'Pawn', 'symbol' => ''],
            ['name' => self::BISHOP, 'title' => 'Bishop', 'symbol' => 'B'],
            ['name' => self::KNIGHT, 'title' => 'Knight', 'symbol' => 'N'],
            ['name' => self::ROOK, 'title' => 'Rook', 'symbol' => 'R'],
            ['name' => self::QUEEN, 'title' => 'Queen', 'symbol' => 'Q'],
            ['name' => self::KING, 'title' => 'King', 'symbol' => 'K'],
        ];

        return new ChessPieceNameCollection(collect($pieces)->mapInto(ChessPieceNameEntity::class));
    }
}
