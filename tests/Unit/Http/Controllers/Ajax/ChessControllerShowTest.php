<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers\Ajax;

use App\Services\ChessGameService;
use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceFactory;
use Database\Factories\ChessGamePieceMoveFactory;
use Mockery\MockInterface;
use Tests\TestCase;

class ChessControllerShowTest extends TestCase
{
    public function testFormatting(): void
    {
        $game = ChessGameFactory::new()->make();

        $game->pieces->add(ChessGamePieceFactory::new()->make());
        $game->moves->add(ChessGamePieceMoveFactory::new()->make());

        $this->mock(ChessGameService::class, function (MockInterface $mock) use ($game) {
            $mock->shouldReceive('getGameByIdWithRelations')->andReturn($game);
        });

        $response = $this->get('/chess-games/ajax/1');

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'id', 'name',
            'moves',
            'pieces' => [
                ['id', 'name', 'color', 'coordinates' => ['x',  'y']]
            ],
        ]);
    }
}
