<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ChessPieceMoveCalculators;

use App\Services\ChessPieceMoveCalculators\PawnMoveCalculator;
use Database\Factories\ChessGamePieceFactory;

class PawnMoveCalculatorTest extends AbstractChessPieceMoveTest
{
    public function testBlockedByAnotherFigure(): void
    {
        /**
         *  h | . . . . . . . . |
         *  g | . . . . . . . . |
         *  f | . . . . . . . . |
         *  e | . . . . . . . . |
         *  d | . . . . . . . . |
         *  c | . . . P . . . . |
         *  b | . . . P . . . . |
         *  a | . . . . . . . . |
         *      1 2 3 4 5 6 7 8
         */

        $pawn_to_test = ChessGamePieceFactory::new()
            ->light()->pawn()
            ->coordinates(4, 2)
            ->make();

        $game = $this->makeGameWithPieces([
            $pawn_to_test,
            ChessGamePieceFactory::new()
                ->light()->pawn()
                ->coordinates(4, 3)
                ->make()
        ]);

        $move_collection = $this->getCalculator()->calculateMovesForPiece($pawn_to_test, $game);

        $expected_coordinates = [];

        $this->assertCoordinatesCollectionEquals($expected_coordinates, $move_collection);
    }

    public function testTwoSquareMoveBlockedByAnotherFigure(): void
    {
        /**
         *  h | . . . . . . . . |
         *  g | . . . . . . . . |
         *  f | . . . . . . . . |
         *  e | . . . . . . . . |
         *  d | . . . P . . . . |
         *  c | . . . * . . . . |
         *  b | . . . P . . . . |
         *  a | . . . . . . . . |
         *      1 2 3 4 5 6 7 8
         */

        $pawn_to_test = ChessGamePieceFactory::new()
            ->light()->pawn()
            ->coordinates(4, 2)
            ->make();

        $game = $this->makeGameWithPieces([
            $pawn_to_test,
            ChessGamePieceFactory::new()
                ->light()->pawn()
                ->coordinates(4, 4)
                ->make()
        ]);

        $move_collection = $this->getCalculator()->calculateMovesForPiece($pawn_to_test, $game);

        $expected_coordinates = [
            [4, 3],
        ];

        $this->assertCoordinatesCollectionEquals($expected_coordinates, $move_collection);
    }

    private function getCalculator(): PawnMoveCalculator
    {
        return app(PawnMoveCalculator::class);
    }
}
