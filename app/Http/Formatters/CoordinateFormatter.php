<?php

declare(strict_types=1);

namespace App\Http\Formatters;

use App\DTO\ChessPieceMoves;
use App\DTO\Collections\CoordinatesCollection;

class CoordinateFormatter
{
    public function formatMoves(ChessPieceMoves $moves): array
    {
        return [
            'movements'   => $this->coordinateCollectionToArray($moves->movement_coordinates_collection),
            'captures'    => $this->coordinateCollectionToArray($moves->capture_coordinates_collection),
            'en_passants' => $this->coordinateCollectionToArray($moves->en_passant_coordinates_collection),
            'castlings'   => $this->coordinateCollectionToArray($moves->castling_coordinates_collection),
        ];
    }

    private function coordinateCollectionToArray(CoordinatesCollection $collection): array
    {
        $result = [];

        foreach ($collection->all() as $coordinates) {
            $result[] = [
                'x' => $coordinates->x,
                'y' => $coordinates->y,
            ];
        }

        return $result;
    }
}
