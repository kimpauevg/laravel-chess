<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators\Traits;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\ValueObjects\ChessPieceMoves;
use App\Services\ValueObjects\Collections\CoordinatesCollection;
use App\Services\ValueObjects\CoordinateModifiers;
use App\Services\ValueObjects\Coordinates;

trait MovesInDirectionUntilObstacleTrait
{
    use ChecksCoordinatesTrait;

    public function calculateMovesForPiece(ChessGamePiece $piece, ChessGame $game): ChessPieceMoves
    {
        $this->setGamePieces($game);

        $moves = new ChessPieceMoves();

        /** @var CoordinateModifiers $coordinate_modifiers */
        foreach ($this->getCoordinateModifiers() as $coordinate_modifiers) {
            $new_moves = $this->findPieceMovesInDirectionUsingModifiers($piece, $coordinate_modifiers);
            $moves->merge($new_moves);
        }

        return $moves;
    }

    private function findPieceMovesInDirectionUsingModifiers(
        ChessGamePiece $piece,
        CoordinateModifiers $modifiers,
    ): ChessPieceMoves {
        $coordinate_x = $piece->coordinate_x;
        $coordinate_y = $piece->coordinate_y;

        $movements_collection = new CoordinatesCollection();

        do {
            $coordinate_x += $modifiers->x_modifier;
            $coordinate_y += $modifiers->y_modifier;

            $coordinates = new Coordinates($coordinate_x, $coordinate_y);

            if ($this->isCoordinateInvalid($coordinates)) {
                break;
            }

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $movements_collection->add($coordinates);
        } while (true);

        $moves = new ChessPieceMoves();
        $moves->movement_coordinates_collection = $movements_collection;

        return $moves;
    }
}
