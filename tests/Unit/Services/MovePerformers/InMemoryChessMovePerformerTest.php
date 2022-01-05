<?php

declare(strict_types=1);

namespace Tests\Unit\Services\MovePerformers;

use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\DTO\ChessPieceMoveData;
use App\DTO\Coordinates;
use App\Models\Collections\ChessGamePieceMoveCollection;
use App\Services\MovePerformers\InMemoryChessMovePerformer;
use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceFactory;
use Database\Factories\ChessGamePieceMoveFactory;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InMemoryChessMovePerformerTest extends TestCase
{
    public function testPerformMovement(): void
    {
        $light_knight = ChessGamePieceFactory::new()
            ->id(1)
            ->light()->knight()
            ->coordinates(1, 1)
            ->make();

        $game = ChessGameFactory::new()->make();

        $game->pieces->add($light_knight);

        $move_data = new  ChessPieceMoveData($game, $light_knight, new Coordinates(2, 3));

        $this->getPerformer()->performMove($move_data);

        $this->assertEquals(2, $light_knight->coordinate_x);
        $this->assertEquals(3, $light_knight->coordinate_y);

        $move = $move_data->game->moves->first();

        $this->assertNotNull($move);
        $this->assertEquals(1, $move->previous_coordinate_x);
        $this->assertEquals(1, $move->previous_coordinate_y);
        $this->assertEquals(2, $move->coordinate_x);
        $this->assertEquals(3, $move->coordinate_y);
        $this->assertEquals(ChessPieceNameDictionary::KNIGHT, $move->chess_piece_name);
    }

    public function testPerformCapture(): void
    {
        $light_queen = ChessGamePieceFactory::new()
            ->id(1)
            ->light()->queen()
            ->coordinates(1, 1)
            ->make();

        $dark_queen = ChessGamePieceFactory::new()
            ->id(1)
            ->dark()->queen()
            ->coordinates(1, 2)
            ->make();

        $game = ChessGameFactory::new()->make();
        $game->pieces->add($light_queen)->add($dark_queen);

        $move_data = new ChessPieceMoveData($game, $light_queen, new Coordinates(1, 2));

        $this->getPerformer()->performMove($move_data);

        $this->assertTrue($dark_queen->is_captured);

        $this->assertEquals(1, $light_queen->coordinate_x);
        $this->assertEquals(2, $light_queen->coordinate_y);

        $move = $move_data->game->moves->first();

        $this->assertNotNull($move);
        $this->assertTrue($move->is_capture);
    }

    public function testPerformEnPassant(): void
    {
        $light_pawn = ChessGamePieceFactory::new()
            ->id(1)
            ->light()->pawn()
            ->coordinates(1, 4)
            ->make();

        $dark_pawn = ChessGamePieceFactory::new()
            ->id(2)
            ->dark()->pawn()
            ->coordinates(2, 4)
            ->make();

        $game = ChessGameFactory::new()->make();
        $game->pieces->add($light_pawn)->add($dark_pawn);

        $previous_move = ChessGamePieceMoveFactory::new()
            ->pawn()
            ->previousCoordinates(1, 2)
            ->newCoordinates(1, 4)
            ->moveIndex(1)
            ->make();

        $game->moves = new ChessGamePieceMoveCollection([$previous_move]);

        $move_data = new ChessPieceMoveData($game, $dark_pawn, new Coordinates(1, 3));

        $this->getPerformer()->performMove($move_data);

        $move = $game->moves->last();
        $this->assertNotNull($move);
        $this->assertEquals(2, $move->move_index);
        $this->assertTrue($move->is_en_passant);
        $this->assertTrue($move->is_capture);
    }

    public function testPerformPromotion(): void
    {
        $light_pawn = ChessGamePieceFactory::new()
            ->id(1)
            ->light()->pawn()
            ->coordinates(1, 7)
            ->make();

        $game = ChessGameFactory::new()->make();

        $game->pieces->add($light_pawn);

        $move_data = new ChessPieceMoveData($game, $light_pawn, new Coordinates(1, 8));
        $move_data->promotion_to_piece_name = ChessPieceNameDictionary::QUEEN;

        $this->getPerformer()->performMove($move_data);

        $this->assertEquals(ChessPieceNameDictionary::QUEEN, $light_pawn->name);
        $this->assertEquals(8, $light_pawn->coordinate_y);
        $this->assertEquals(ChessPieceNameDictionary::QUEEN, $game->moves->first()?->promotion?->to_name);
    }

    public function testPerformLeftCastling(): void
    {
        $light_king = ChessGamePieceFactory::new()
            ->id(1)
            ->light()->king()
            ->coordinates(5, 1)
            ->make();

        $light_rook = ChessGamePieceFactory::new()
            ->id(2)
            ->light()->rook()
            ->coordinates(1, 1)
            ->make();

        $game = ChessGameFactory::new()->make();
        $game->pieces->add($light_king)->add($light_rook);

        $move_data = new ChessPieceMoveData($game, $light_king, new Coordinates(3, 1));

        $this->getPerformer()->performMove($move_data);

        $this->assertEquals(4, $light_rook->coordinate_x);
        $this->assertEquals(3, $light_king->coordinate_x);
    }

    public function testPerformRightCastling(): void
    {
        $light_king = ChessGamePieceFactory::new()
            ->id(1)
            ->light()->king()
            ->coordinates(5, 1)
            ->make();

        $light_rook = ChessGamePieceFactory::new()
            ->id(2)
            ->light()->rook()
            ->coordinates(8, 1)
            ->make();

        $game = ChessGameFactory::new()->make();
        $game->pieces->add($light_king)->add($light_rook);

        $move_data = new ChessPieceMoveData($game, $light_king, new Coordinates(7, 1));

        $this->getPerformer()->performMove($move_data);

        $this->assertEquals(7, $light_king->coordinate_x);
        $this->assertEquals(6, $light_rook->coordinate_x);
    }

    public function testIncorrectMove(): void
    {
        $pawn = ChessGamePieceFactory::new()
            ->id(1)
            ->light()->pawn()
            ->coordinates(1, 2)
            ->make();

        $game = ChessGameFactory::new()->make();
        $game->pieces->add($pawn);

        $move_data = new ChessPieceMoveData($game, $pawn, new Coordinates(2, 2));

        try {
            $this->getPerformer()->performMove($move_data);
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('coordinates', $e->errors());

            return;
        }

        $this->fail('Should have thrown exception');
    }

    private function getPerformer(): InMemoryChessMovePerformer
    {
        return $this->app->make(InMemoryChessMovePerformer::class);
    }
}
