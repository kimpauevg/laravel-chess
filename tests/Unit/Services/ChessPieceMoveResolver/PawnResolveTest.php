<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ChessPieceMoveResolver;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGamePiece;

class PawnResolveTest extends AbstractResolveTest
{
    public function testLightMoveFromStartingPosition(): void
    {
        $piece = $this->makeLightPawnWithCoordinates(4, 2);

        $resolver = $this->makeResolverForEmptyTable($piece);

        $collection = $resolver->getPossibleMovesCoordinates();

        /**
         *  h | . . . . . . . . |
         *  g | . . . . . . . . |
         *  f | . . . . . . . . |
         *  e | . . . . . . . . |
         *  d | . . . + . . . . |
         *  c | . . . + . . . . |
         *  b | . . . P . . . . |
         *  a | . . . . . . . . |
         *      1 2 3 4 5 6 7 8
         */
        $expected_coordinates = [
            [4, 3],
            [4, 4],
        ];

        $this->assertCoordinatesCollectionEquals($expected_coordinates, $collection);
    }

    public function testLightMoveFromNonStartingPosition(): void
    {
        $piece = $this->makeLightPawnWithCoordinates(4, 3);

        $resolver = $this->makeResolverForEmptyTable($piece);

        $collection = $resolver->getPossibleMovesCoordinates();

        /**
         *  h | . . . . . . . . |
         *  g | . . . . . . . . |
         *  f | . . . . . . . . |
         *  e | . . . . . . . . |
         *  d | . . . + . . . . |
         *  c | . . . P . . . . |
         *  b | . . . . . . . . |
         *  a | . . . . . . . . |
         *      1 2 3 4 5 6 7 8
         */
        $expected_coordinates = [
            [4, 4],
        ];

        $this->assertCoordinatesCollectionEquals($expected_coordinates, $collection);
    }

    public function testDarkMoveFromStartingPosition(): void
    {
        $piece = $this->makeDarkPawnWithCoordinates(4, 7);

        $resolver = $this->makeResolverForEmptyTable($piece);

        $collection = $resolver->getPossibleMovesCoordinates();

        /**
         *  h | . . . . . . . . |
         *  g | . . . P . . . . |
         *  f | . . . + . . . . |
         *  e | . . . + . . . . |
         *  d | . . . . . . . . |
         *  c | . . . . . . . . |
         *  b | . . . . . . . . |
         *  a | . . . . . . . . |
         *      1 2 3 4 5 6 7 8
         */
        $expected_coordinates = [
            [4, 6],
            [4, 5],
        ];

        $this->assertCoordinatesCollectionEquals($expected_coordinates, $collection);
    }

    public function testDarkMoveFromNonStartingPosition(): void
    {
        $piece = $this->makeDarkPawnWithCoordinates(4, 6);

        $resolver = $this->makeResolverForEmptyTable($piece);

        $collection = $resolver->getPossibleMovesCoordinates();

        /**
         *  h | . . . . . . . . |
         *  g | . . . . . . . . |
         *  f | . . . P . . . . |
         *  e | . . . + . . . . |
         *  d | . . . . . . . . |
         *  c | . . . . . . . . |
         *  b | . . . . . . . . |
         *  a | . . . . . . . . |
         *      1 2 3 4 5 6 7 8
         */
        $expected_coordinates = [
            [4, 5],
        ];

        $this->assertCoordinatesCollectionEquals($expected_coordinates, $collection);
    }

    private function makeDarkPawnWithCoordinates(int $x, int $y): ChessGamePiece
    {
        return $this->makePieceWithCoordinates(
            ChessPieceDictionary::PAWN,
            ChessPieceDictionary::COLOR_DARK,
            $x,
            $y
        );
    }

    private function makeLightPawnWithCoordinates(int $x, int $y): ChessGamePiece
    {
        return $this->makePieceWithCoordinates(
            ChessPieceDictionary::PAWN,
            ChessPieceDictionary::COLOR_LIGHT,
            $x,
            $y
        );
    }
}
