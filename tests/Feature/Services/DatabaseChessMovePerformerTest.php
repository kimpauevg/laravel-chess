<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\DTO\ChessMoveDataDTO;
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
         *  h | K . . . . . . . |
         *  g | . . R . . . . P |
         *  f | . Q . . . . . . |
         *  e | . . . . . . . . |
         *  d | . . . . . . . . |
         *  c | . . . . . . . . |
         *  b | . . . . . . . . |
         *  a | . . . . . . . . |
         *      1 2 3 4 5 6 7 8
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

        /** @var DatabaseChessMovePerformer $performer */
        $performer = $this->app->make(DatabaseChessMovePerformer::class);

        $move_dto = new ChessMoveDataDTO($game, $light_queen, new Coordinates(2, 7));

        $performer->performMove($move_dto);

        $this->assertDatabaseHas(ChessGamePieceMove::TABLE, [
            'is_mate' => true,
        ]);
    }
}
