<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ChessPieceMoveResolver;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGamePiece;

class QueenResolveTest extends AbstractResolveTest
{
    public function testQueenMovesFromCenter(): void
    {
        $piece = $this->makeLightQueenWithCoordinates(4, 4);

        $game = $this->makeResolverForEmptyTable($piece);

        $collection = $game->getPossibleMovesCoordinates();

        /**
         *  h | . . . + . . . + |
         *  g | + . . + . . + . |
         *  f | . + . + . + . . |
         *  e | . . + + + . . . |
         *  d | + + + Q + + + + |
         *  c | . . + + + . . . |
         *  b | . + . + . + . . |
         *  a | + . . + . . + . |
         *      1 2 3 4 5 6 7 8
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

        $this->assertCoordinatesCollectionEquals($expected_coordinates, $collection);
    }

    private function makeLightQueenWithCoordinates(int $x, int $y): ChessGamePiece
    {
        return $this->makeGamePieceWithCoordinates(
            ChessPieceDictionary::QUEEN,
            ChessPieceDictionary::COLOR_LIGHT,
            $x,
            $y
        );
    }
}
