<?php

declare(strict_types=1);

namespace Tests\Unit\Services\MoveCalculators;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Models\ChessGamePiece;

class KnightMoveCalculatorTest extends AbstractChessPieceMoveCalculatorTest
{
    public function testAllPossibleMoves(): void
    {
        $piece = $this->makeLightKnightWithCoordinates(4, 4);

        $collection = $this->getMovesOnEmptyTableForPiece($piece);
        /**
         *  8 | . . . . . . . . |
         *  7 | . . . . . . . . |
         *  6 | . . + . + . . . |
         *  5 | . + . . . + . . |
         *  4 | . . . N . . . . |
         *  3 | . + . . . + . . |
         *  2 | . . + . + . . . |
         *  1 | . . . . . . . . |
         *      a b c d e f g h
         */
        $expected_coordinates = [
            [5, 6], [6, 5], [6, 3], [5, 2],
            [3, 2], [2, 3], [2, 5], [3, 6],
        ];

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $collection);
    }

    public function testMovesFromCorner(): void
    {
        $piece = $this->makeLightKnightWithCoordinates(1, 1);

        $collection = $this->getMovesOnEmptyTableForPiece($piece);

        /**
         *  8 | . . . . . . . . |
         *  7 | . . . . . . . . |
         *  6 | . . . . . . . . |
         *  5 | . . . . . . . . |
         *  4 | . . . . . . . . |
         *  3 | . + . . . . . . |
         *  2 | . . + . . . . . |
         *  1 | N . . . . . . . |
         *      a b c d e f g h
         */
        $expected_coordinates = [
            [2, 3],
            [3, 2],
        ];

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $collection);
    }

    private function makeLightKnightWithCoordinates(int $x, int $y): ChessGamePiece
    {
        return $this->makeGamePieceWithCoordinates(
            ChessPieceNameDictionary::KNIGHT,
            ChessPieceColorDictionary::LIGHT,
            $x,
            $y
        );
    }
}
