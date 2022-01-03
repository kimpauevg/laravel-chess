<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Ajax;

use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceFactory;
use Illuminate\Support\Arr;
use Tests\Feature\TestCase;

class ChessControllerGetChessPieceMovesTest extends TestCase
{
    public function testFormatting(): void
    {
        ChessGameFactory::new()->id(1)
            ->hasPieces(
                ChessGamePieceFactory::new()->id(1)
                    ->light()->pawn()
                    ->coordinates(1, 2)
            )
            ->create();

        $response = $this->get('/chess-games/ajax/1/piece/1/moves');

        $movements = Arr::get($response->json(), 'movements');

        $this->assertEquals([
            ['x' => 1, 'y' => 3],
            ['x' => 1, 'y' => 4],
        ], $movements);
    }
}
