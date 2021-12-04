<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGamePieceCollection;
use App\Services\ValueObjects\Collections\CoordinateCollection;
use App\Services\ValueObjects\Coordinates;

abstract class AbstractChessPieceMoveCalculator
{
    protected ChessGame $game;
    protected ChessGamePieceCollection $all_pieces;

    abstract public function calculateMovesForPiece(ChessGamePiece $piece, ChessGame $game): CoordinateCollection;

    protected function setGamePieces(ChessGame $game): void
    {
        $this->all_pieces = $game->pieces;
    }

    protected function isCoordinateInvalid(Coordinates $coordinates): bool
    {
        return $coordinates->x <= 0 || $coordinates->x > 8
            || $coordinates->y <= 0 || $coordinates->y > 8;
    }

    protected function getFromPossibleCoordinates(array $possible_coordinates): CoordinateCollection
    {
        $valid_coordinates = [];

        foreach ($possible_coordinates as $coordinates) {
            if ($this->isCoordinateInvalid($coordinates)) {
                continue;
            }

            $valid_coordinates[] = $coordinates;
        }

        $collection = new CoordinateCollection();

        foreach ($valid_coordinates as $coordinates) {
            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                continue;
            }

            $collection->add($coordinates);
        }

        return $collection;
    }

    protected function isGridWithCoordinatesTaken(Coordinates $coordinates): bool
    {
        return !is_null($this->getChessPieceWithCoordinates($coordinates));
    }

    protected function getChessPieceWithCoordinates(Coordinates $coordinates): ?ChessGamePiece
    {
        return $this->all_pieces->whereCoordinateX($coordinates->x)->whereCoordinateY($coordinates->y)->first();
    }
}
