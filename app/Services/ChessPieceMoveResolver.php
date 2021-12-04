<?php

declare(strict_types=1);

namespace App\Services;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGamePieceCollection;
use App\Services\ValueObjects\Collections\CoordinateCollection;
use App\Services\ValueObjects\Coordinates;

class ChessPieceMoveResolver
{
    private ChessGamePiece $piece;
    private ChessGamePieceCollection $all_pieces;

    public function __construct(ChessGamePiece $piece, ChessGame $game)
    {
        $this->piece = $piece;
        $this->all_pieces = $game->pieces;
    }

    public function getPossibleMovesCoordinates(): CoordinateCollection
    {
        $piece = $this->piece;

        switch ($piece->name) {
            case ChessPieceDictionary::ROOK:
                return $this->getRookMoves();

            case ChessPieceDictionary::BISHOP:
                return $this->getBishopMoves();

            case ChessPieceDictionary::QUEEN:
                return $this->getRookMoves()->merge($this->getBishopMoves());

            case ChessPieceDictionary::PAWN:
                return $this->getPawnMoves();

            case ChessPieceDictionary::KNIGHT:
                return $this->getKnightMoves();

            case ChessPieceDictionary::KING:
                return $this->getKingMoves();

            default:
                return new CoordinateCollection();
        }
    }

    private function getKnightMoves(): CoordinateCollection
    {
        $x = $this->piece->coordinate_x;
        $y = $this->piece->coordinate_y;

        $possible_coordinates = [
            new Coordinates($x + 1, $y + 2),
            new Coordinates($x + 2, $y + 1),
            new Coordinates($x + 2, $y - 1),
            new Coordinates($x + 1, $y - 2),
            new Coordinates($x - 1, $y - 2),
            new Coordinates($x - 2, $y - 1),
            new Coordinates($x - 2, $y + 1),
            new Coordinates($x - 1, $y + 2),
        ];

        return $this->getFromPossibleCoordinates($possible_coordinates);
    }

    private function getKingMoves(): CoordinateCollection
    {
        $x = $this->piece->coordinate_x;
        $y = $this->piece->coordinate_y;

        $possible_coordinates = [
            new Coordinates($x, $y + 1),
            new Coordinates($x + 1, $y + 1),
            new Coordinates($x + 1, $y),
            new Coordinates($x + 1, $y - 1),
            new Coordinates($x, $y - 1),
            new Coordinates($x - 1, $y - 1),
            new Coordinates($x - 1, $y),
            new Coordinates($x - 1, $y + 1),
        ];

        return $this->getFromPossibleCoordinates($possible_coordinates);
    }

    private function getFromPossibleCoordinates(array $possible_coordinates): CoordinateCollection
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

    private function isCoordinateInvalid(Coordinates $coordinates): bool
    {
        return $coordinates->x <= 0 || $coordinates->x > 8
            || $coordinates->y <= 0 || $coordinates->y > 8;
    }

    private function getPawnMoves(): CoordinateCollection
    {
        $collection = new CoordinateCollection();

        $piece = $this->piece;

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

    private function getRookMoves(): CoordinateCollection
    {
        $collection = new CoordinateCollection();

        $piece = $this->piece;

        for ($coordinate_x = $piece->coordinate_x + 1; $coordinate_x <= 8; $coordinate_x++) {
            $coordinates = new Coordinates($coordinate_x, $piece->coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        for ($coordinate_x = $piece->coordinate_x - 1; $coordinate_x > 0; $coordinate_x--) {
            $coordinates = new Coordinates($coordinate_x, $piece->coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        for ($coordinate_y = $piece->coordinate_y + 1; $coordinate_y <= 8; $coordinate_y++) {
            $coordinates= new Coordinates($piece->coordinate_x, $coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        for ($coordinate_y = $piece->coordinate_y - 1; $coordinate_y > 0; $coordinate_y--) {
            $coordinates= new Coordinates($piece->coordinate_x, $coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        return $collection;
    }

    private function getBishopMoves(): CoordinateCollection
    {
        $collection = new CoordinateCollection();

        $piece = $this->piece;

        $coordinate_x = $piece->coordinate_x;
        $coordinate_y = $piece->coordinate_y;

        while (++$coordinate_x <= 8 && ++$coordinate_y <= 8) {
            $coordinates= new Coordinates($coordinate_x, $coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        $coordinate_x = $piece->coordinate_x;
        $coordinate_y = $piece->coordinate_y;

        while (++$coordinate_x <= 8 && --$coordinate_y > 0) {
            $coordinates= new Coordinates($coordinate_x, $coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        $coordinate_x = $piece->coordinate_x;
        $coordinate_y = $piece->coordinate_y;

        while (--$coordinate_x > 0 && --$coordinate_y > 0) {
            $coordinates= new Coordinates($coordinate_x, $coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        $coordinate_x = $piece->coordinate_x;
        $coordinate_y = $piece->coordinate_y;

        while (--$coordinate_x > 0 && ++$coordinate_y <= 8) {
            $coordinates = new Coordinates($coordinate_x, $coordinate_y);

            if ($this->isGridWithCoordinatesTaken($coordinates)) {
                break;
            }

            $collection->add($coordinates);
        }

        return $collection;
    }

    private function isGridWithCoordinatesTaken(Coordinates $coordinates): bool
    {
        return !is_null($this->getChessPieceWithCoordinates($coordinates));
    }

    private function getChessPieceWithCoordinates(Coordinates $coordinate): ?ChessGamePiece
    {
        return $this->all_pieces->whereCoordinateX($coordinate->x)->whereCoordinateY($coordinate->y)->first();
    }
}
