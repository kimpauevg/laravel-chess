<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Formatters\CoordinateFormatter;
use App\Services\ChessGameService;
use Illuminate\Http\JsonResponse;

class ChessController extends Controller
{
    public function getChessPieceMoves(
        int $id,
        int $chess_piece_id,
        ChessGameService $service,
        CoordinateFormatter $formatter
    ): JsonResponse {
        $collection = $service->getPossibleMovesForChessPieceById($id, $chess_piece_id);

        return response()->json(
            $formatter->formatCollection($collection),
        );
    }
}
