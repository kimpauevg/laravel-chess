<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ChessPieceMoveCalculators;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Dictionaries\ChessPieceCoordinates\ChessPieceCoordinateDictionary;
use App\Models\ChessGamePiece;

class KingMoveCalculatorTest extends AbstractChessPieceMoveCalculatorTest
{
    public function testMovesFromCenter(): void
    {
        $piece = $this->makeLightKingWithCoordinates(4, 2);

        $moves = $this->getMovesOnEmptyTableForPiece($piece);

        /**
         *  h | . . . . . . . . |
         *  g | . . . . . . . . |
         *  f | . . . . . . . . |
         *  e | . . . . . . . . |
         *  d | . . . . . . . . |
         *  c | . . + + + . . . |
         *  b | . . + K + . . . |
         *  a | . . + + + . . . |
         *      1 2 3 4 5 6 7 8
         */
        $expected_coordinates = [
            [4, 3],
            [5, 3],
            [5, 2],
            [5, 1],
            [4, 1],
            [3, 1],
            [3, 2],
            [3, 3],
        ];

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $moves);
    }

    public function testMovesFromCorner(): void
    {
        $piece = $this->makeLightKingWithCoordinates(
            ChessPieceCoordinateDictionary::MIN_COORDINATE_X,
            ChessPieceCoordinateDictionary::MIN_COORDINATE_Y
        );

        $moves = $this->getMovesOnEmptyTableForPiece($piece);

        /**
         *  h | . . . . . . . . |
         *  g | . . . . . . . . |
         *  f | . . . . . . . . |
         *  e | . . . . . . . . |
         *  d | . . . . . . . . |
         *  c | . . . . . . . . |
         *  b | + + . . . . . . |
         *  a | K + . . . . . . |
         *      1 2 3 4 5 6 7 8
         */

        $expected_coordinates = [
            [1, 2],
            [2, 2],
            [2, 1],
        ];

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $moves);
    }

    private function makeLightKingWithCoordinates(int $x, int $y): ChessGamePiece
    {
        return $this->makeGamePieceWithCoordinates(
            ChessPieceNameDictionary::KING,
            ChessPieceColorDictionary::LIGHT,
            $x,
            $y,
        );
    }
}
