<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGamePiece;
use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceFactory;
use Tests\Feature\TestCase;

class ChessControllerMovePieceTest extends TestCase
{
    public function testBishopAllowedMove(): void
    {
        ChessGameFactory::new(['id' => 1])
            ->hasPieces(
                ChessGamePieceFactory::new([
                    'id' => 1,
                    'coordinate_x' => 1,
                    'coordinate_y' => 1,
                    'name' => ChessPieceDictionary::BISHOP,
                ])
            )
            ->create();

        $response = $this->post('/chess-games/1/piece/1/move', [
            'coordinates' => [
                'x' => 8,
                'y' => 8,
            ],
        ]);

        $response->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas(ChessGamePiece::TABLE, [
            'id' => 1,
            'coordinate_x' => 8,
            'coordinate_y' => 8,
        ]);
    }
}
