<?php

declare(strict_types=1);

namespace App\Http\Formatters;

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
}
