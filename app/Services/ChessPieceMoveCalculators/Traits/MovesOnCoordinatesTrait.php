<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators\Traits;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\ValueObjects\Collections\CoordinatesCollection;

trait MovesOnCoordinatesTrait
{
    use ChecksCoordinatesTrait;

    public function calculateMovesForPiece(ChessGamePiece $piece, ChessGame $game): CoordinatesCollection
    {
        $this->setGamePieces($game);

        return $this->filterProposedCoordinates($this->getCoordinatesForPiece($piece));
    }

    protected function filterProposedCoordinates(array $possible_coordinates): CoordinatesCollection
    {
        $valid_coordinates = [];

        foreach ($possible_coordinates as $coordinates) {
            if ($this->isCoordinateInvalid($coordinates)) {
                continue;
            }

            $valid_coordinates[] = $coordinates;
        }

        $collection = new CoordinatesCollection();

        foreach ($valid_coordinates as $coordinates) {
            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                continue;
            }

            $collection->add($coordinates);
        }

        return $collection;
    }

    abstract protected function getCoordinatesForPiece(ChessGamePiece $piece): array;
}
