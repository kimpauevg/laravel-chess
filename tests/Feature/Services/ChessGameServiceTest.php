<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Services\ChessGameService;
use Database\Factories\ChessGameFactory;
use Tests\Feature\TestCase;

class ChessGameServiceTest extends TestCase
{
    public function testGetGameByIdWithRelations(): void
    {
        ChessGameFactory::new()->id(1)->create();

        /** @var ChessGameService $service */
        $service = $this->app->make(ChessGameService::class);

        $game = $service->getGameByIdWithRelations(1);

        $this->assertEquals(1, $game->id);
    }
}
