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
            ['name' => self::PAWN, 'title' => 'Pawn'],
            ['name' => self::BISHOP, 'title' => 'Bishop'],
            ['name' => self::KNIGHT, 'title' => 'Knight'],
            ['name' => self::ROOK, 'title' => 'Rook'],
            ['name' => self::QUEEN, 'title' => 'Queen'],
            ['name' => self::KING, 'title' => 'King'],
        ];

        return new ChessPieceNameCollection(collect($pieces)->mapInto(ChessPieceNameEntity::class));
    }
}
