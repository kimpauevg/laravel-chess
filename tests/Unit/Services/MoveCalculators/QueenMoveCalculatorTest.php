<?php

declare(strict_types=1);

namespace Tests\Unit\Services\MoveCalculators;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Models\ChessGamePiece;

class QueenMoveCalculatorTest extends AbstractChessPieceMoveCalculatorTest
{
    public function testQueenMovesFromCenter(): void
    {
        $piece = $this->makeLightQueenWithCoordinates(4, 4);

        $collection = $this->getMovesOnEmptyTableForPiece($piece);

        /**
         *  8 | . . . + . . . + |
         *  7 | + . . + . . + . |
         *  6 | . + . + . + . . |
         *  5 | . . + + + . . . |
         *  4 | + + + Q + + + + |
         *  3 | . . + + + . . . |
         *  2 | . + . + . + . . |
         *  1 | + . . + . . + . |
         *      a b c d e f g h
         */

        $expected_coordinates = [
            // vertical
            [4, 8], [4, 7], [4, 6], [4, 5],
            [4, 3], [4, 2], [4, 1],
            // horizontal
            [1, 4], [2, 4], [3, 4],
            [5, 4], [6, 4], [7, 4], [8, 4],
            // diagonal
            [1, 1], [2, 2], [3, 3],
            [5, 5], [6, 6], [7, 7], [8, 8],
            [1, 7], [2, 6], [3, 5],
            [5, 3], [6, 2], [7, 1],
        ];

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $collection);
    }

    private function makeLightQueenWithCoordinates(int $x, int $y): ChessGamePiece
    {
        return $this->makeGamePieceWithCoordinates(
            ChessPieceNameDictionary::QUEEN,
            ChessPieceColorDictionary::LIGHT,
            $x,
            $y
        );
    }
}
