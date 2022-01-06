<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers\Ajax;

use App\DTO\ChessPieceMoves;
use App\DTO\Coordinates;
use App\Services\ChessGameService;
use Mockery\MockInterface;
use Tests\TestCase;

class ChessControllerGetChessPieceMovesTest extends TestCase
{
    public function testFormatting(): void
    {
        $moves = new ChessPieceMoves();
        $moves->movement_coordinates_collection->add(new Coordinates(1, 2));
        $moves->capture_coordinates_collection->add(new Coordinates(3, 4));
        $moves->en_passant_coordinates_collection->add(new Coordinates(5, 6));
        $moves->castling_coordinates_collection->add(new Coordinates(7, 8));

        $this->mock(ChessGameService::class, function (MockInterface $mock) use ($moves) {
            $mock->shouldReceive('getPossibleMovesForChessPieceById')->andReturn($moves);
        });

        $response = $this->get('/chess-games/ajax/1/piece/1/moves');
        $this->assertEquals([
            'movements' => [
                ['x' => 1, 'y' => 2],
            ],
            'captures' => [
                ['x' => 3, 'y' => 4],
            ],
            'en_passants' => [
                ['x' => 5, 'y' => 6],
            ],
            'castlings' => [
                ['x' => 7, 'y' => 8],
            ],
        ], $response->json());
    }
}
