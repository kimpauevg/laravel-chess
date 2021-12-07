<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators\Traits;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\ValueObjects\ChessPieceMoves;
use App\ValueObjects\Collections\CoordinatesCollection;
use App\ValueObjects\CoordinateModifiers;
use App\ValueObjects\Coordinates;

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

        $moves = new ChessPieceMoves();

        do {
            $coordinate_x += $modifiers->x_modifier;
            $coordinate_y += $modifiers->y_modifier;

            $coordinates = new Coordinates($coordinate_x, $coordinate_y);

            if ($this->isCoordinateInvalid($coordinates)) {
                break;
            }

            $piece_on_coordinates = $this->getChessPieceWithCoordinates($coordinates);

            $piece_on_coordinates_exists = !is_null($piece_on_coordinates);

            if ($piece_on_coordinates_exists && $piece_on_coordinates->color != $piece->color) {
                $moves->capture_coordinates_collection->add($coordinates);
            }

            if ($piece_on_coordinates_exists) {
                break;
            }

            $moves->movement_coordinates_collection->add($coordinates);
        } while (true);

        return $moves;
    }
}
