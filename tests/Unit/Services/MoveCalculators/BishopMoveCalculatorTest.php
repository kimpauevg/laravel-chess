<?php

declare(strict_types=1);

namespace Tests\Unit\Services\MoveCalculators;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Models\ChessGamePiece;

class BishopMoveCalculatorTest extends AbstractChessPieceMoveCalculatorTest
{
    public function testMovesFromCenter(): void
    {
        $bishop = $this->makeLightBishopWithCoordinates(4, 4);

        $moves = $this->getMovesOnEmptyTableForPiece($bishop);

        /**
         *  8 | . . . . . . . + |
         *  7 | + . . . . . + . |
         *  6 | . + . . . + . . |
         *  5 | . . + . + . . . |
         *  4 | . . . B . . . . |
         *  3 | . . + . + . . . |
         *  2 | . + . . . + . . |
         *  1 | + . . . . . + . |
         *      a b c d e f g h
         */

        $expected_coordinates = [
            [5, 5], [6, 6], [7, 7], [8, 8],
            [5, 3], [6, 2], [7, 1],
            [3, 3], [2, 2], [1, 1],
            [3, 5], [2, 6], [1, 7],
        ];

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $moves);
    }

    private function makeLightBishopWithCoordinates(int $x, int $y): ChessGamePiece
    {
        return $this->makeGamePieceWithCoordinates(
            ChessPieceNameDictionary::BISHOP,
            ChessPieceColorDictionary::LIGHT,
            $x,
            $y,
        );
    }
}
