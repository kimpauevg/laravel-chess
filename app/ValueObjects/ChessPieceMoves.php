<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\ValueObjects\Collections\CoordinatesCollection;

class ChessPieceMoves
{
    public CoordinatesCollection $movement_coordinates_collection;
    public CoordinatesCollection $capture_coordinates_collection;
    public CoordinatesCollection $en_passant_coordinates_collection;
    public CoordinatesCollection $promotion_coordinates_collection;
    public CoordinatesCollection $castling_coordinates_collection;

    public function __construct()
    {
        $this->movement_coordinates_collection = new CoordinatesCollection();
        $this->capture_coordinates_collection = new CoordinatesCollection();
        $this->castling_coordinates_collection = new CoordinatesCollection();
        $this->en_passant_coordinates_collection = new CoordinatesCollection();
        $this->promotion_coordinates_collection = new CoordinatesCollection();
    }

    public function merge(ChessPieceMoves $new_moves): ChessPieceMoves
    {
        $moves = new ChessPieceMoves();

        $moves->movement_coordinates_collection = $this->movement_coordinates_collection
            ->merge($new_moves->movement_coordinates_collection);
        $moves->capture_coordinates_collection = $this->capture_coordinates_collection
            ->merge($new_moves->capture_coordinates_collection);
        $moves->castling_coordinates_collection = $this->castling_coordinates_collection
            ->merge($new_moves->castling_coordinates_collection);
        $moves->en_passant_coordinates_collection = $this->en_passant_coordinates_collection
            ->merge($new_moves->en_passant_coordinates_collection);
        $moves->promotion_coordinates_collection = $this->promotion_coordinates_collection
            ->merge($new_moves->promotion_coordinates_collection);

        return $moves;
    }
}
