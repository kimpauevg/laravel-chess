<?php

declare(strict_types=1);

namespace Tests\Unit\Services\MoveCalculators;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Dictionaries\ChessPieceCoordinates\ChessPieceCoordinateDictionary;
use App\Models\ChessGamePiece;
use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceFactory;
use Database\Factories\ChessGamePieceMoveFactory;

class KingMoveCalculatorTest extends AbstractChessPieceMoveCalculatorTest
{
    public function testMovesInAllDirections(): void
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

    public function testLeftCastling(): void
    {
        $light_king = $this->makeLightKingWithCoordinates(5, 1);

        $light_rook = ChessGamePieceFactory::new()
            ->light()->rook()
            ->coordinates(1, 1)
            ->make();

        $game = ChessGameFactory::new()->make();
        $game->pieces->add($light_king)->add($light_rook);

        $moves = $this->getMoveCalculatorFactory()
            ->make($light_king)
            ->calculateMovesForPieceInGame($light_king, $game);

        $castling = $moves->castling_coordinates_collection->first();
        $this->assertNotNull($castling);

        $this->assertEquals(3, $castling->x);
        $this->assertEquals(1, $castling->y);
    }

    public function testRightCastling(): void
    {
        $light_king = $this->makeLightKingWithCoordinates(5, 1);

        $light_rook = ChessGamePieceFactory::new()
            ->light()->rook()
            ->coordinates(8, 1)
            ->make();

        $game = ChessGameFactory::new()->make();

        $game->pieces->add($light_king)->add($light_rook);

        $moves = $this->getMoveCalculatorFactory()
            ->make($light_king)
            ->calculateMovesForPieceInGame($light_king, $game);

        $castling = $moves->castling_coordinates_collection->first();
        $this->assertNotNull($castling);

        $this->assertEquals(7, $castling->x);
        $this->assertEquals(1, $castling->y);
    }

    public function testCastlingDeniedKingMoved(): void
    {
        $light_king = $this->makeLightKingWithCoordinates(5, 1);

        $left_light_rook = ChessGamePieceFactory::new()
            ->light()->rook()
            ->coordinates(1, 1)
            ->make();

        $right_light_rook = ChessGamePieceFactory::new()
            ->light()->rook()
            ->coordinates(8, 1)
            ->make();

        $game = ChessGameFactory::new()->make();
        $game->pieces->add($light_king)->add($left_light_rook)->add($right_light_rook);

        $king_move = ChessGamePieceMoveFactory::new()
            ->king()
            ->moveIndex(1)
            ->make();
        $game->moves->add($king_move);

        $moves = $this->getMoveCalculatorFactory()
            ->make($light_king)
            ->calculateMovesForPieceInGame($light_king, $game);

        $this->assertEquals(0, $moves->castling_coordinates_collection->count());
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
