<?php

declare(strict_types=1);

namespace App\Http\Formatters;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGameCollection;

class ChessGameFormatter
{
    public function collectionToList(ChessGameCollection $collection): array
    {
        $result = [];

        foreach ($collection->all() as $chess_game) {
            $result[] = [
                'id'   => $chess_game->id,
                'name' => $chess_game->name,
            ];
        }

        return $result;
    }

    public function oneWithPieces(ChessGame $game): array
    {
        $result = $this->getGameAttributes($game);

        $pieces = [];

        foreach ($game->pieces as $piece) {
            $pieces[] = $this->getChessPieceAttributes($piece);
        }

        $result['pieces'] = $pieces;

        return $result;
    }

    private function getGameAttributes(ChessGame $game): array
    {
        return [
            'id'   => $game->id,
            'name' => $game->name,
        ];
    }

    private function getChessPieceAttributes(ChessGamePiece $piece): array
    {
        return [
            'id'          => $piece->id,
            'coordinates' => [
                'x' => $piece->coordinate_x,
                'y' => $piece->coordinate_y,
            ],
            'chess_piece' => $piece->name,
            'color'       => $piece->color,
        ];
    }
}
