<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators\Traits;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\ValueObjects\ChessPieceMoves;
use App\Services\ValueObjects\Collections\CoordinatesCollection;

trait MovesOnCoordinatesTrait
{
    use ChecksCoordinatesTrait;

    public function calculateMovesForPiece(ChessGamePiece $piece, ChessGame $game): ChessPieceMoves
    {
        $this->setGamePieces($game);

        return $this->getMovesFromCoordinates($this->getCoordinatesForPiece($piece));
    }

    protected function getMovesFromCoordinates(array $possible_coordinates): ChessPieceMoves
    {
        $valid_coordinates = [];

        foreach ($possible_coordinates as $coordinates) {
            if ($this->isCoordinateInvalid($coordinates)) {
                continue;
            }

            $valid_coordinates[] = $coordinates;
        }

        $movements_collection = new CoordinatesCollection();

        foreach ($valid_coordinates as $coordinates) {
            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                continue;
            }

            $movements_collection->add($coordinates);
        }

        $moves = new ChessPieceMoves();
        $moves->movement_coordinates_collection = $movements_collection;

        return $moves;
    }

    abstract protected function getCoordinatesForPiece(ChessGamePiece $piece): array;
}
