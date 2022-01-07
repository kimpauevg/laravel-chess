<?php

declare(strict_types=1);

namespace App\Http\Formatters;

use App\Dictionaries\ChessPieceNames\ChessPieceNameCollection;
use App\Dictionaries\ChessPieceNames\ChessPieceNameEntity;

class ChessPieceNameFormatter
{
    public function formatCollection(ChessPieceNameCollection $collection): array
    {
        return $collection
            ->map(function (ChessPieceNameEntity $piece_name) {
                return $this->formatOne($piece_name);
            })
            ->values()
            ->all();
    }

    private function formatOne(ChessPieceNameEntity $piece_name): array
    {
        return [
            'name'   => $piece_name->name,
            'title'  => $piece_name->title,
            'symbol' => $piece_name->symbol,
        ];
    }
}
