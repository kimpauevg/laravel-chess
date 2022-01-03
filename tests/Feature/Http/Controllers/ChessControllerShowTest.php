<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceFactory;
use Tests\Feature\TestCase;

class ChessControllerShowTest extends TestCase
{
    public function testShow(): void
    {
        ChessGameFactory::new(['id' => 1, 'name' => 'Test Game'])->id(1)
            ->name('Test Game')
            ->hasPieces(
                ChessGamePieceFactory::new()
                    ->name(ChessPieceNameDictionary::ROOK)->light()
            )
            ->create();

        $response = $this->get('/chess-games/1');

        $response->assertSuccessful();
    }
}
