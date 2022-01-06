<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers;

use App\Models\Builders\ChessGameBuilder;
use Database\Factories\ChessGameFactory;
use Mockery\Mock;
use Mockery\MockInterface;
use Tests\TestCase;

class ChessControllerShowTest extends TestCase
{
    public function testPassedData(): void
    {
        $game = ChessGameFactory::new()->id(1)->make();

        $this->mockGameSearch($game);

        $response = $this->get('/chess-games/1');

        $response->assertSuccessful();

        $response->assertViewHas('chess_game.id', 1);
        $response->assertViewHas('dictionaries.promotable_chess_piece_names.0.name');
    }
}
