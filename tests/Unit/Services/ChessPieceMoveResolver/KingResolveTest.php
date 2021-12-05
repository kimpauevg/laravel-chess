<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ChessPieceMoveResolver;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGamePiece;

class KingResolveTest extends AbstractResolveTest
{
    public function testMovesFromCenter(): void
    {
        $piece = $this->makeLightKingWithCoordinates(4, 2);

        $resolver = $this->makeResolverForEmptyTable($piece);

        $collection = $resolver->getPossibleMovesCoordinates();

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

        $this->assertCoordinatesCollectionEquals($expected_coordinates, $collection);
    }

    public function testMovesFromCorner(): void
    {
        $piece = $this->makeLightKingWithCoordinates(
            ChessPieceDictionary::MIN_COORDINATE_X,
            ChessPieceDictionary::MIN_COORDINATE_Y
        );

        $resolver = $this->makeResolverForEmptyTable($piece);

        $collection = $resolver->getPossibleMovesCoordinates();

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

        $this->assertCoordinatesCollectionEquals($expected_coordinates, $collection);
    }

    private function makeLightKingWithCoordinates(int $x, int $y): ChessGamePiece
    {
        return $this->makeGamePieceWithCoordinates(
            ChessPieceDictionary::KING,
            ChessPieceDictionary::COLOR_LIGHT,
            $x,
            $y,
        );
    }
}
