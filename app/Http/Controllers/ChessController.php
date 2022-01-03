<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Http\Formatters\ChessGameFormatter;
use App\Http\Requests\MakeMoveRequest;
use App\Http\Requests\StoreChessGameRequest;
use App\Models\Collections\ChessGameCollection;
use App\Services\ChessGameService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ChessController extends Controller
{
    public function index(ChessGameFormatter $formatter, ChessGameService $service): View
    {
        $paginator = $service->getChessGames();

        /** @var ChessGameCollection $collection */
        $collection = $paginator->getCollection();

        return view('chess-game.index', [
            'chess_games' => $formatter->formatCollection($collection),
            'pagination'  => $this->formatPaginator($paginator),
        ]);
    }

    public function show(
        int $id,
        ChessGameService $service,
        ChessGameFormatter $formatter,
        ChessPieceNameDictionary $chess_piece_dictionary
    ): View {
        $game = $service->getGameById($id);

        return view('chess-game.show', [
            'chess_game'   => $formatter->formatOneWithPieces($game),
            'dictionaries' => [
                'promotable_chess_piece_names' => $chess_piece_dictionary->all()->whereCanBePromotedTo()
            ]
        ]);
    }

    public function store(StoreChessGameRequest $request, ChessGameService $service): RedirectResponse
    {
        $game = $service->storeGame($request->validated());

        return redirect(route('chess-games.show', ['id' => $game->id]))
            ->with('success', 'New Game has been created!');
    }

    /**
     * @throws ValidationException
     */
    public function movePiece(
        int $id,
        int $chess_piece_id,
        MakeMoveRequest $request,
        ChessGameService $service
    ): RedirectResponse {
        $service->makeMove($id, $chess_piece_id, $request->validated());

        return back();
    }
}
