<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Builders\ChessGameBuilder;
use App\Models\ChessGame;
use App\Models\Collections\ChessGamePieceCollection;
use App\Models\Collections\ChessGamePieceMoveCollection;
use App\Services\ChessGameService;
use Database\Factories\ChessGamePieceFactory;
use Database\Factories\ChessGamePieceMoveFactory;
use Mockery\MockInterface;
use Tests\TestCase;

class ChessGameServiceTest extends TestCase
{
    public function testCheckSolvedByCapture(): void
    {
        $game = new ChessGame();

        $dark_knight = ChessGamePieceFactory::new()
            ->id(1)
            ->dark()->knight()
            ->coordinates(1, 8)
            ->make();

        $dark_king = ChessGamePieceFactory::new()
            ->id(2)
            ->dark()->king()
            ->coordinates(1, 6)
            ->make();

        $light_knight = ChessGamePieceFactory::new()
            ->id(3)
            ->light()->knight()
            ->coordinates(3, 7)
            ->make();

        $game->pieces = new ChessGamePieceCollection([$dark_king, $dark_knight, $light_knight]);

        $prev_move = ChessGamePieceMoveFactory::new()
            ->knight()
            ->previousCoordinates(2, 5)
            ->newCoordinates(3, 7)
            ->moveIndex(1)
            ->make();

        $game->moves = new ChessGamePieceMoveCollection([$prev_move]);

        $this->mock(ChessGameBuilder::class, function (MockInterface $mock) use ($game) {
            $mock->shouldReceive('findOrFail')->andReturn($game);
        });

        /** @var ChessGameService $service */
        $service = $this->app->make(ChessGameService::class);

        $moves = $service->getPossibleMovesForChessPieceById(1, 1);

        $this->assertEquals(1, $moves->capture_coordinates_collection->count());

        $capture_move = $moves->capture_coordinates_collection->first();
        $this->assertEquals(3, $capture_move->x);
        $this->assertEquals(7, $capture_move->y);
    }
}
