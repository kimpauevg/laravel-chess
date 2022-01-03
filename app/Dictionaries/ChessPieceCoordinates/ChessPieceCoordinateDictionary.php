<?php

declare(strict_types=1);

namespace App\Dictionaries\ChessPieceCoordinates;

class ChessPieceCoordinateDictionary
{
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
}
