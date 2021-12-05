<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ChessPieceMoveResolver;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGamePiece;

class KnightResolveTest extends AbstractResolveTest
{
    public function testAllPossibleMoves(): void
    {
        $piece = $this->makeLightKnightWithCoordinates(4, 4);

        $resolver = $this->makeResolverForEmptyTable($piece);

        $collection = $resolver->getPossibleMovesCoordinates();
        /**
         *  h | . . . . . . . . |
         *  g | . . . . . . . . |
         *  f | . . + . + . . . |
         *  e | . + . . . + . . |
         *  d | . . . Kn. . . . |
         *  c | . + . . . + . . |
         *  b | . . + . + . . . |
         *  a | . . . . . . . . |
         *      1 2 3 4 5 6 7 8
         */
        $expected_coordinates = [
            [5, 6],
            [6, 5],
            [6, 3],
            [5, 2],
            [3, 2],
            [2, 3],
            [2, 5],
            [3, 6],
        ];

        $this->assertCoordinatesCollectionEquals($expected_coordinates, $collection);
    }

    public function testMovesFromCorner(): void
    {
        $piece = $this->makeLightKnightWithCoordinates(1, 1);

        $resolver = $this->makeResolverForEmptyTable($piece);

        $collection = $resolver->getPossibleMovesCoordinates();

        /**
         *  h | . . . . . . . . |
         *  g | . . . . . . . . |
         *  f | . . . . . . . . |
         *  e | . . . . . . . . |
         *  d | . . . . . . . . |
         *  c | . + . . . . . . |
         *  b | . . + . . . . . |
         *  a | Kn. . . . . . . |
         *      1 2 3 4 5 6 7 8
         */
        $expected_coordinates = [
            [2, 3],
            [3, 2],
        ];

        $this->assertCoordinatesCollectionEquals($expected_coordinates, $collection);
    }

    private function makeLightKnightWithCoordinates(int $x, int $y): ChessGamePiece
    {
        return $this->makeGamePieceWithCoordinates(
            ChessPieceDictionary::KNIGHT,
            ChessPieceDictionary::COLOR_LIGHT,
            $x,
            $y
        );
    }
}
