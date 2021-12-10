<?php

declare(strict_types=1);

namespace App\Dictionaries\ChessPieces;

class ChessPieceDictionary
{
    public const
        PAWN = 'pawn',
        BISHOP = 'bishop',
        KNIGHT = 'knight',
        ROOK = 'rook',
        QUEEN = 'queen',
        KING = 'king'
    ;

    public const
        COLOR_DARK = 'dark',
        COLOR_LIGHT = 'light'
    ;

    private const
        MIN_COORDINATE = 1,
        MAX_COORDINATE = 8
    ;

    public const
        MIN_COORDINATE_X = self::MIN_COORDINATE,
        MAX_COORDINATE_X = self::MAX_COORDINATE,
        MIN_COORDINATE_Y = self::MIN_COORDINATE,
        MAX_COORDINATE_Y = self::MAX_COORDINATE
    ;

    public const
        LIGHT_PIECE_STARTING_Y_COORDINATE = self::MIN_COORDINATE_Y,
        DARK_PIECE_STARTING_Y_COORDINATE = self::MAX_COORDINATE_Y,
        LIGHT_PAWN_STARTING_Y_COORDINATE = 2,
        DARK_PAWN_STARTING_Y_COORDINATE = 7
    ;

    public function names(): ChessPieceCollection
    {
        $pieces = [
            ['name' => self::PAWN, 'title' => 'Pawn'],
            ['name' => self::BISHOP, 'title' => 'Bishop'],
            ['name' => self::KNIGHT, 'title' => 'Knight'],
            ['name' => self::ROOK, 'title' => 'Rook'],
            ['name' => self::QUEEN, 'title' => 'Queen'],
            ['name' => self::KING, 'title' => 'King'],
        ];

        return new ChessPieceCollection(collect($pieces)->mapInto(ChessPieceEntity::class));
    }
}
