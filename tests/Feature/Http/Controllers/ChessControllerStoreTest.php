<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use Tests\Feature\TestCase;

class ChessControllerStoreTest extends TestCase
{
    public function testStore(): void
    {
        $response = $this->post('/chess-games', ['name' => 'Test Game']);

        $response->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas(ChessGame::TABLE, [
            'name' => 'Test Game',
        ]);

        $this->assertDatabaseCount(ChessGamePiece::TABLE, 32);
    }
}
