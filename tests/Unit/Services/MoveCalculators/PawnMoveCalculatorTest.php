<?php

declare(strict_types=1);

namespace Tests\Unit\Services\MoveCalculators;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGamePieceMoveCollection;
use App\Services\MoveCalculators\PawnMoveCalculator;
use Database\Factories\ChessGamePieceFactory;
use Database\Factories\ChessGamePieceMoveFactory;

class PawnMoveCalculatorTest extends AbstractChessPieceMoveCalculatorTest
{
    public function testLightMoveFromStartingPosition(): void
    {
        $piece = $this->makeLightPawnWithCoordinates(4, 2);

        $collection = $this->getMovesOnEmptyTableForPiece($piece);

        /**
         *  8 | . . . . . . . . |
         *  7 | . . . . . . . . |
         *  6 | . . . . . . . . |
         *  5 | . . . . . . . . |
         *  4 | . . . + . . . . |
         *  3 | . . . + . . . . |
         *  2 | . . . P . . . . |
         *  1 | . . . . . . . . |
         *      a b c d e f g h
         */
        $expected_coordinates = [
            [4, 3],
            [4, 4],
        ];

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $collection);
    }

    public function testLightMoveFromNonStartingPosition(): void
    {
        $piece = $this->makeLightPawnWithCoordinates(4, 3);

        $collection = $this->getMovesOnEmptyTableForPiece($piece);

        /**
         *  8 | . . . . . . . . |
         *  7 | . . . . . . . . |
         *  6 | . . . . . . . . |
         *  5 | . . . . . . . . |
         *  4 | . . . + . . . . |
         *  3 | . . . P . . . . |
         *  2 | . . . . . . . . |
         *  1 | . . . . . . . . |
         *      a b c d e f g h
         */
        $expected_coordinates = [
            [4, 4],
        ];

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $collection);
    }

    public function testDarkMoveFromStartingPosition(): void
    {
        $piece = $this->makeDarkPawnWithCoordinates(4, 7);

        $collection = $this->getMovesOnEmptyTableForPiece($piece);

        /**
         *  8 | . . . . . . . . |
         *  7 | . . . P . . . . |
         *  6 | . . . + . . . . |
         *  5 | . . . + . . . . |
         *  4 | . . . . . . . . |
         *  3 | . . . . . . . . |
         *  2 | . . . . . . . . |
         *  1 | . . . . . . . . |
         *      a b c d e f g h
         */
        $expected_coordinates = [
            [4, 6],
            [4, 5],
        ];

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $collection);
    }

    public function testDarkMoveFromNonStartingPosition(): void
    {
        $piece = $this->makeDarkPawnWithCoordinates(4, 6);

        $collection = $this->getMovesOnEmptyTableForPiece($piece);

        /**
         *  8 | . . . . . . . . |
         *  7 | . . . . . . . . |
         *  6 | . . . P . . . . |
         *  5 | . . . + . . . . |
         *  4 | . . . . . . . . |
         *  3 | . . . . . . . . |
         *  2 | . . . . . . . . |
         *  1 | . . . . . . . . |
         *      a b c d e f g h
         */
        $expected_coordinates = [
            [4, 5],
        ];

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $collection);
    }

    private function makeDarkPawnWithCoordinates(int $x, int $y): ChessGamePiece
    {
        return $this->makeGamePieceWithCoordinates(
            ChessPieceNameDictionary::PAWN,
            ChessPieceColorDictionary::DARK,
            $x,
            $y
        );
    }

    private function makeLightPawnWithCoordinates(int $x, int $y): ChessGamePiece
    {
        return $this->makeGamePieceWithCoordinates(
            ChessPieceNameDictionary::PAWN,
            ChessPieceColorDictionary::LIGHT,
            $x,
            $y
        );
    }

    public function testBlockedByAnotherFigure(): void
    {
        /**
         *  8 | . . . . . . . . |
         *  7 | . . . . . . . . |
         *  6 | . . . . . . . . |
         *  5 | . . . . . . . . |
         *  4 | . . . . . . . . |
         *  3 | . . . P . . . . |
         *  2 | . . . P . . . . |
         *  1 | . . . . . . . . |
         *      a b c d e f g h
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

        $move_collection = $this->getCalculator()->calculateMovesForPieceInGame($pawn_to_test, $game);

        $expected_coordinates = [];

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $move_collection);
    }

    public function testTwoSquareMoveBlockedByAnotherFigure(): void
    {
        /**
         *  8 | . . . . . . . . |
         *  7 | . . . . . . . . |
         *  6 | . . . . . . . . |
         *  5 | . . . . . . . . |
         *  4 | . . . P . . . . |
         *  3 | . . . * . . . . |
         *  2 | . . . P . . . . |
         *  1 | . . . . . . . . |
         *      a b c d e f g h
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

        $move_collection = $this->getCalculator()->calculateMovesForPieceInGame($pawn_to_test, $game);

        $expected_coordinates = [
            [4, 3],
        ];

        $this->assertMovesMovementCollectionEquals($expected_coordinates, $move_collection);
    }

    public function testEnPassant(): void
    {
        /**
         *  8 | . . . . . . . . |
         *  7 | . . . . . . . . |
         *  6 | . . . . * . . . |
         *  5 | . . . P P . . . |
         *  4 | . . . . . . . . |
         *  3 | . . . . . . . . |
         *  2 | . . . . . . . . |
         *  1 | . . . . . . . . |
         *      a b c d e f g h
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

        $result = $calculator->calculateMovesForPieceInGame($pawn_to_test, $game);

        $expected_coordinates = [
            [5, 6]
        ];

        $this->assertCoordinateCollectionEquals($expected_coordinates, $result->en_passant_coordinates_collection);
    }

    private function getCalculator(): PawnMoveCalculator
    {
        return $this->app->make(PawnMoveCalculator::class);
    }
}
