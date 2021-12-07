<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ChessPieceMoveCalculators;

use App\Models\Collections\ChessGamePieceMoveCollection;
use App\Services\ChessPieceMoveCalculators\PawnMoveCalculator;
use Database\Factories\ChessGamePieceFactory;
use Database\Factories\ChessGamePieceMoveFactory;

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

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $move_collection);
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

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $move_collection);
    }

    public function testEnPassante(): void
    {
        /**
         *  h | . . . . . . . . |
         *  g | . . . . . . . . |
         *  f | . . . . * . . . |
         *  e | . . . P P . . . |
         *  d | . . . . . . . . |
         *  c | . . . . . . . . |
         *  b | . . . . . . . . |
         *  a | . . . . . . . . |
         *      1 2 3 4 5 6 7 8
         */

        $pawn_to_test = ChessGamePieceFactory::new()
            ->light()->pawn()
            ->coordinates(4, 5)
            ->make();

        $game = $this->makeGameWithPieces([
            $pawn_to_test,
            ChessGamePieceFactory::new()
                ->light()->pawn()
                ->coordinates(5, 5)
                ->make()
        ]);

        $last_move = ChessGamePieceMoveFactory::new()->pawn()
            ->previousCoordinates(5, 7)
            ->newCoordinates(5, 5)
            ->make();

        $game->setRelation('moves', new ChessGamePieceMoveCollection([$last_move]));

        $calculator = $this->getCalculator();

        $result = $calculator->calculateMovesForPiece($pawn_to_test, $game);

        $expected_coordinates = [
            [5, 6]
        ];
        $this->assertMovesCapturesCollectionEquals($expected_coordinates, $result);
    }

    private function getCalculator(): PawnMoveCalculator
    {
        return app(PawnMoveCalculator::class);
    }
}
