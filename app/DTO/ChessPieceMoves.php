<?php

declare(strict_types=1);

namespace App\DTO;

use App\DTO\Collections\CoordinatesCollection;

class ChessPieceMoves
{
    public CoordinatesCollection $movement_coordinates_collection;
    public CoordinatesCollection $capture_coordinates_collection;
    public CoordinatesCollection $en_passant_coordinates_collection;
    public CoordinatesCollection $castling_coordinates_collection;

    public function __construct()
    {
        $this->movement_coordinates_collection = new CoordinatesCollection();
        $this->capture_coordinates_collection = new CoordinatesCollection();
        $this->castling_coordinates_collection = new CoordinatesCollection();
        $this->en_passant_coordinates_collection = new CoordinatesCollection();
    }
}
