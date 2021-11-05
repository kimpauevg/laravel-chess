<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Formatters\ChessGameFormatter;
use App\Models\Collections\ChessGameCollection;
use App\Services\ChessGameService;
use Illuminate\View\View;

class ChessController extends Controller
{
    public function index(ChessGameFormatter $formatter, ChessGameService $service): View
    {
        $paginator = $service->getChessGames();

        /** @var ChessGameCollection $collection */
        $collection = $paginator->getCollection();

        return view('chess-game.index', [
            'chess_games' => $formatter->collectionToList($collection),
        ]);
    }
}
