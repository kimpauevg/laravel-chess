<?php

declare(strict_types=1);

namespace App\Services\MoveCalculators\Traits;

use App\Dictionaries\ChessPieceCoordinates\ChessPieceCoordinateDictionary;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGamePieceCollection;
use App\DTO\Coordinates;

trait ChecksCoordinatesTrait
{
    protected ChessGamePieceCollection $all_pieces;

    protected function setGamePieces(ChessGame $game): void
    {
        $this->all_pieces = $game->pieces;
    }

    protected function isGridWithCoordinatesTaken(Coordinates $coordinates): bool
    {
        return !is_null($this->getChessPieceWithCoordinates($coordinates));
    }

    protected function getChessPieceWithCoordinates(Coordinates $coordinates): ?ChessGamePiece
    {
        return $this->all_pieces->whereCoordinateX($coordinates->x)->whereCoordinateY($coordinates->y)->first();
    }

    protected function isCoordinateInvalid(Coordinates $coordinates): bool
    {
        return $coordinates->x < ChessPieceCoordinateDictionary::MIN_COORDINATE_X
            || $coordinates->x > ChessPieceCoordinateDictionary::MAX_COORDINATE_X
            || $coordinates->y < ChessPieceCoordinateDictionary::MIN_COORDINATE_Y
            || $coordinates->y > ChessPieceCoordinateDictionary::MAX_COORDINATE_Y;
    }
}
