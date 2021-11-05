<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Builders\ChessGameBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

class ChessGameService
{
    public function getChessGames(): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = $this->getBuilder()->paginate();

        return $paginator;
    }

    private function getBuilder(): ChessGameBuilder
    {
        return app(ChessGameBuilder::class);
    }
}
