<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\ValueObjects\Collections\CoordinateCollection;
use App\Services\ValueObjects\Coordinates;

class PawnMoveCalculator extends AbstractChessPieceMoveCalculator
{
    public function calculateMovesForPiece(ChessGamePiece $piece, ChessGame $game): CoordinateCollection
    {
        $this->setGamePieces($game);

        $collection = new CoordinateCollection();

        if ($piece->color === ChessPieceDictionary::COLOR_LIGHT) {
            $collection->add(new Coordinates($piece->coordinate_x, $piece->coordinate_y + 1));
        }

        if ($piece->color === ChessPieceDictionary::COLOR_LIGHT && $piece->coordinate_y === 2) {
            $collection->add(new Coordinates($piece->coordinate_x, 4));
        }

        if ($piece->color === ChessPieceDictionary::COLOR_DARK) {
            $collection->add(new Coordinates($piece->coordinate_x, $piece->coordinate_y - 1));
        }

        if ($piece->color === ChessPieceDictionary::COLOR_DARK && $piece->coordinate_y === 7) {
            $collection->add(new Coordinates($piece->coordinate_x, 5));
        }

        return $collection;
    }
}
