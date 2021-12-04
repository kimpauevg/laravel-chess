<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Ajax;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceFactory;
use Tests\Feature\TestCase;

class ChessControllerGetChessPieceMovesTest extends TestCase
{
    public function testFormatting(): void
    {
        ChessGameFactory::new(['id' => 1])
            ->hasPieces(
                ChessGamePieceFactory::new([
                    'id' => 1,
                    'coordinate_x' => 1,
                    'coordinate_y' => 2,
                    'color' => ChessPieceDictionary::COLOR_LIGHT,
                    'name' => ChessPieceDictionary::PAWN,
                ])
            )
            ->create();

        $response = $this->get('/chess-games/ajax/1/piece/1/moves');

        $this->assertEquals([
            ['x' => 1, 'y' => 3],
            ['x' => 1, 'y' => 4],
        ], $response->json());
    }
}
