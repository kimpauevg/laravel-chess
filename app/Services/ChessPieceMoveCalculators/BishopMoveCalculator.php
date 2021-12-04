<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGamePiece;
use App\Services\ValueObjects\Collections\CoordinateCollection;
use App\Services\ValueObjects\Coordinates;

class BishopMoveCalculator extends AbstractChessPieceMoveCalculator
{
    public function calculateMovesForPiece(ChessGamePiece $piece): CoordinateCollection
    {
        $collection = new CoordinateCollection();

        $coordinate_x = $piece->coordinate_x;
        $coordinate_y = $piece->coordinate_y;

        while (++$coordinate_x <= ChessPieceDictionary::MAX_COORDINATE_X && ++$coordinate_y <= ChessPieceDictionary::MAX_COORDINATE_Y) {
            $coordinates= new Coordinates($coordinate_x, $coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        $coordinate_x = $piece->coordinate_x;
        $coordinate_y = $piece->coordinate_y;

        while (++$coordinate_x <= ChessPieceDictionary::MAX_COORDINATE_X && --$coordinate_y >= ChessPieceDictionary::MIN_COORDINATE_Y) {
            $coordinates= new Coordinates($coordinate_x, $coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        $coordinate_x = $piece->coordinate_x;
        $coordinate_y = $piece->coordinate_y;

        while (--$coordinate_x >= ChessPieceDictionary::MIN_COORDINATE_X && --$coordinate_y >= ChessPieceDictionary::MIN_COORDINATE_Y) {
            $coordinates= new Coordinates($coordinate_x, $coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        $coordinate_x = $piece->coordinate_x;
        $coordinate_y = $piece->coordinate_y;

        while (--$coordinate_x >= ChessPieceDictionary::MIN_COORDINATE_X && ++$coordinate_y <= ChessPieceDictionary::MAX_COORDINATE_Y) {
            $coordinates = new Coordinates($coordinate_x, $coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        return $collection;
    }
}
