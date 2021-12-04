<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGamePiece;
use App\Services\ValueObjects\Collections\CoordinateCollection;
use App\Services\ValueObjects\Coordinates;

class RookMoveCalculator extends AbstractChessPieceMoveCalculator
{
    public function calculateMovesForPiece(ChessGamePiece $piece): CoordinateCollection
    {
        $collection = new CoordinateCollection();

        for ($coordinate_x = $piece->coordinate_x + 1; $coordinate_x <= ChessPieceDictionary::MAX_COORDINATE_X; $coordinate_x++) {
            $coordinates = new Coordinates($coordinate_x, $piece->coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        for ($coordinate_x = $piece->coordinate_x - 1; $coordinate_x >= ChessPieceDictionary::MIN_COORDINATE_X; $coordinate_x--) {
            $coordinates = new Coordinates($coordinate_x, $piece->coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        for ($coordinate_y = $piece->coordinate_y + 1; $coordinate_y <= ChessPieceDictionary::MAX_COORDINATE_Y; $coordinate_y++) {
            $coordinates= new Coordinates($piece->coordinate_x, $coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        for ($coordinate_y = $piece->coordinate_y - 1; $coordinate_y >= ChessPieceDictionary::MIN_COORDINATE_Y; $coordinate_y--) {
            $coordinates = new Coordinates($piece->coordinate_x, $coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        return $collection;
    }
}
