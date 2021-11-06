<?php

declare(strict_types=1);

namespace Tests\Unit\Dictionaries\ChessPieces;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use Tests\TestCase;

class ChessPieceDictionaryTest extends TestCase
{
    public function testStartingPiecesGeneration(): void
    {
        /** @var ChessPieceDictionary $dictionary */
        $dictionary = app(ChessPieceDictionary::class);

        $pieces = $dictionary->getStartingPiecesCollection();

        $light_pawns = $pieces->whereName(ChessPieceDictionary::PAWN)
            ->whereCoordinateY(2)
            ->whereColor(ChessPieceDictionary::COLOR_LIGHT)
            ->count();
        $this->assertEquals(8, $light_pawns);

        $dark_pawns = $pieces->whereName(ChessPieceDictionary::PAWN)
            ->whereCoordinateY(7)
            ->whereColor(ChessPieceDictionary::COLOR_DARK)
            ->count();
        $this->assertEquals(8, $dark_pawns);

        $correct_name_order = [
            ChessPieceDictionary::ROOK,
            ChessPieceDictionary::KNIGHT,
            ChessPieceDictionary::BISHOP,
            ChessPieceDictionary::QUEEN,
            ChessPieceDictionary::KING,
            ChessPieceDictionary::BISHOP,
            ChessPieceDictionary::KNIGHT,
            ChessPieceDictionary::ROOK,
        ];

        $light_pieces_names_order = $pieces->whereCoordinateY(1)
            ->whereColor(ChessPieceDictionary::COLOR_LIGHT)
            ->sortBy('coordinate_x')
            ->pluck('name')
            ->toArray();

        $dark_pieces_names_order = $pieces->whereCoordinateY(8)
            ->whereColor(ChessPieceDictionary::COLOR_DARK)
            ->sortBy('coordinate_x')
            ->pluck('name')
            ->toArray();

        $this->assertEquals($correct_name_order, $light_pieces_names_order);
        $this->assertEquals($correct_name_order, $dark_pieces_names_order);
    }
}
