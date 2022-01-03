<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\ChessGame;
use Database\Factories\ChessGameFactory;
use Mockery\MockInterface;
use Tests\Feature\TestCase;

class ChessControllerIndexTest extends TestCase
{
    public function testIndex(): void
    {
        // arrange
        ChessGameFactory::new(['id' => 1, 'name' => 'Test Game'])->create();

        // act
        $response = $this->get('/');

        // assert
        $response->assertSuccessful();

        $response->assertSee('Test Game');
        $response->assertSee('#1');
    }

    public function testPaginationRendering(): void
    {
        // arrange
        ChessGameFactory::new()->count(5)->create();

        $this->mock(ChessGame::class, function (MockInterface $mock) {
            $mock->shouldReceive('getPerPage')->andReturn(2);
        });

        // act
        $response = $this->get('/?page=2');

        // assert
        $response->assertSee('/?page=1');
        $response->assertSee('/?page=3');
    }
}
