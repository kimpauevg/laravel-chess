<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\DTO\ChessPieceMoveData;
use App\DTO\Coordinates;
use App\Models\ChessGamePieceMove;
use App\Services\MovePerformers\DatabaseChessMovePerformer;
use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceFactory;
use Tests\Feature\TestCase;

class DatabaseChessMovePerformerTest extends TestCase
{
    public function testPerformMate(): void
    {
        /**
         *  8 | K . . . . . . . |
         *  7 | . . R . . . . P |
         *  6 | . Q . . . . . . |
         *  5 | . . . . . . . . |
         *  4 | . . . . . . . . |
         *  3 | . . . . . . . . |
         *  2 | . . . . . . . . |
         *  1 | . . . . . . . . |
         *      a b c d e f g h
         */

        $game = ChessGameFactory::new()->id(1)
            ->hasPieces(
                ChessGamePieceFactory::new()->id(1)
                    ->dark()->king()
                    ->coordinates(1, 8)
            )
            ->hasPieces(
                ChessGamePieceFactory::new()->id(2)
                    ->light()->queen()
                    ->coordinates(2, 6)
            )
            ->hasPieces(
                ChessGamePieceFactory::new()->id(3)
                    ->light()->rook()
                    ->coordinates(3, 7)
            )
            ->hasPieces(
                ChessGamePieceFactory::new()->id(4)
                    ->dark()->pawn()
                    ->coordinates(8, 7)
            )
            ->create();

        $light_queen = $game->pieces->findOrFail(2);

        $move_data = new ChessPieceMoveData($game, $light_queen, new Coordinates(2, 7));

        $this->getPerformer()->performMove($move_data);

        $this->assertDatabaseHas(ChessGamePieceMove::TABLE, [
            'is_check' => true,
            'is_mate'  => true,
            'is_draw'  => false,
        ]);
    }

    public function testPerformDraw(): void
    {
        /**
         *  8 | K . . . . . . . |
         *  7 | . . R . . . . . |
         *  6 | . . . . . . . . |
         *  5 | . Q . . . . . . |
         *  4 | . . . . . . . . |
         *  3 | . . . . . . . . |
         *  2 | . . . . . . . . |
         *  1 | . . . . . . . . |
         *      a b c d e f g h
         */

        $game = ChessGameFactory::new()->id(1)
            ->hasPieces(
                ChessGamePieceFactory::new()->id(1)
                    ->dark()->king()
                    ->coordinates(1, 8)
            )
            ->hasPieces(
                ChessGamePieceFactory::new()->id(2)
                    ->light()->queen()
                    ->coordinates(2, 5)
            )
            ->hasPieces(
                ChessGamePieceFactory::new()->id(3)
                    ->light()->rook()
                    ->coordinates(3, 7)
            )
            ->create();

        $light_queen = $game->pieces->findOrFail(2);

        $move_data = new ChessPieceMoveData($game, $light_queen, new Coordinates(2, 6));

        $this->getPerformer()->performMove($move_data);

        $this->assertDatabaseHas(ChessGamePieceMove::TABLE, [
            'is_check' => false,
            'is_mate'  => false,
            'is_draw'  => true,
        ]);
    }

    public function testPerformCheck(): void
    {
        /**
         *  8 | K . . . . . . . |
         *  7 | . . R . . . . . |
         *  6 | . . . . . . . . |
         *  5 | . . . . . . . . |
         *  4 | . . . . . . . . |
         *  3 | . . . . . . . . |
         *  2 | . . . . . . . . |
         *  1 | . . . . . . . . |
         *      a b c d e f g h
         */

        $game = ChessGameFactory::new()->id(1)
            ->hasPieces(
                ChessGamePieceFactory::new()->id(1)
                    ->dark()->king()
                    ->coordinates(1, 8)
            )
            ->hasPieces(
                ChessGamePieceFactory::new()->id(2)
                    ->light()->rook()
                    ->coordinates(3, 7)
            )
            ->create();

        $rook = $game->pieces->findOrFail(2);

        $move_data = new ChessPieceMoveData($game, $rook, new Coordinates(3, 8));

        $this->getPerformer()->performMove($move_data);

        $this->assertDatabaseHas(ChessGamePieceMove::TABLE, [
            'is_check' => true,
            'is_mate'  => false,
            'is_draw'  => false,
        ]);
    }

    private function getPerformer(): DatabaseChessMovePerformer
    {
        return $this->app->make(DatabaseChessMovePerformer::class);
    }
}
