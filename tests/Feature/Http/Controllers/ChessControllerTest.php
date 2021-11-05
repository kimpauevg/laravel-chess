<?php

declare(strict_types=1);

use Database\Factories\ChessGameFactory;
use Tests\Feature\TestCase;

class ChessControllerTest extends TestCase
{
    public function testIndex(): void
    {
        ChessGameFactory::new(['id' => 1, 'name' => 'Test Game'])->create();
        $response = $this->get('/chess');

        $response->assertSuccessful();

        $response->assertSee('Test Game');
        $response->assertSee('#1');
    }
}
