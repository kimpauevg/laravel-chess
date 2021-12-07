<?php

declare(strict_types=1);

namespace App\Dictionaries\ChessPieces;

use Illuminate\Support\Arr;

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

    public function getStartingPiecesCollection(): ChessPieceCollection
    {
        $light_pawns = [];
        $dark_pawns = [];

        $dark_pieces = [];
        $light_pieces = [];

        $pieces_by_coordinate_x = [
            1 => self::ROOK,
            2 => self::KNIGHT,
            3 => self::BISHOP,
            4 => self::QUEEN,
            5 => self::KING,
            6 => self::BISHOP,
            7 => self::KNIGHT,
            8 => self::ROOK,
        ];

        for ($coordinate_x = 1; $coordinate_x <= 8; $coordinate_x++) {
            $light_pawns[] = [
                'color'       => self::COLOR_LIGHT,
                'name'        => self::PAWN,
                'coordinates' => [
                    'x' => $coordinate_x,
                    'y' => 2,
                ],
            ];

            $dark_pawns[] = [
                'color'       => self::COLOR_DARK,
                'name'        => self::PAWN,
                'coordinates' => [
                    'x' => $coordinate_x,
                    'y' => 7,
                ],
            ];

            $light_pieces[] = [
                'color'       => self::COLOR_LIGHT,
                'name'        => Arr::get($pieces_by_coordinate_x, $coordinate_x),
                'coordinates' => [
                    'x' => $coordinate_x,
                    'y' => 1,
                ],
            ];

            $dark_pieces[] = [
                'color'       => self::COLOR_DARK,
                'name'        => Arr::get($pieces_by_coordinate_x, $coordinate_x),
                'coordinates' => [
                    'x' => $coordinate_x,
                    'y' => 8,
                ],
            ];
        }

        $all_pieces = array_merge($dark_pawns, $dark_pieces, $light_pawns, $light_pieces);

        $mapped_pieces = collect($all_pieces)->mapInto(ChessPieceEntity::class);

        return new ChessPieceCollection($mapped_pieces);
    }
}
