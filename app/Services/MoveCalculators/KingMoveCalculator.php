<?php

declare(strict_types=1);

namespace App\Services\MoveCalculators;

use App\Dictionaries\ChessPieceCoordinates\ChessPieceCoordinateDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\DTO\Collections\CoordinatesCollection;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\MoveCalculators\Traits\MovesOnCoordinatesTrait;
use App\DTO\ChessPieceMoves;
use App\DTO\Coordinates;

class KingMoveCalculator extends AbstractChessPieceMoveCalculator
{
    use MovesOnCoordinatesTrait;

    public function calculateMovesForPieceInGame(ChessGamePiece $piece, ChessGame $game): ChessPieceMoves
    {
        $this->setGamePieces($game);

        $moves = $this->getMovesFromCoordinates($this->getCoordinatesForPiece($piece), $piece);

        $moves->castling_coordinates_collection = $this->getCastlingMoves($piece, $game);

        return $moves;
    }

    private function getCastlingMoves(ChessGamePiece $king, ChessGame $game): CoordinatesCollection
    {
        $coordinates_collection = new CoordinatesCollection();

        if ($game->moves->whereKing()->whereColor($king->color)->exists()) {
            return $coordinates_collection;
        }

        $rooks = $game->pieces->whereName(ChessPieceNameDictionary::ROOK)->whereColor($king->color);

        $left_rook_exists = $rooks->whereCoordinateX(ChessPieceCoordinateDictionary::MIN_COORDINATE_X)
            ->exists();

        $rook_moves = $game->moves->whereRook();

        $left_rook_didnt_move = $rook_moves->wherePreviousCoordinates(
            ChessPieceCoordinateDictionary::MIN_COORDINATE_X,
            $king->coordinate_y
        )->doesNotExist();

        $no_pieces_between_king_and_left_rook = $game->pieces
            ->whereCoordinateY($king->coordinate_y)
            ->whereCoordinateXBetween(ChessPieceCoordinateDictionary::MIN_COORDINATE_X, $king->coordinate_x)
            ->doesNotExist();

        if ($left_rook_exists
            && $left_rook_didnt_move
            && $no_pieces_between_king_and_left_rook
        ) {
            $coordinates_collection->add(new Coordinates($king->coordinate_x - 2, $king->coordinate_y));
        }

        $right_rook_exists = $rooks->whereCoordinateX(ChessPieceCoordinateDictionary::MAX_COORDINATE_X)
            ->exists();

        $right_rook_didnt_move = $rook_moves->wherePreviousCoordinates(
            ChessPieceCoordinateDictionary::MAX_COORDINATE_X,
            $king->coordinate_y
        )->doesNotExist();

        $no_pieces_between_king_and_right_rook = $game->pieces
            ->whereCoordinateY($king->coordinate_y)
            ->whereCoordinateXBetween($king->coordinate_x, ChessPieceCoordinateDictionary::MAX_COORDINATE_X)
            ->doesNotExist();

        if ($right_rook_exists
            && $right_rook_didnt_move
            && $no_pieces_between_king_and_right_rook
        ) {
            $coordinates_collection->add(new Coordinates($king->coordinate_x + 2, $king->coordinate_y));
        }

        return $coordinates_collection;
    }

    protected function getCoordinatesForPiece(ChessGamePiece $piece): array
    {
        $x = $piece->coordinate_x;
        $y = $piece->coordinate_y;

        return [
            new Coordinates($x, $y + 1),
            new Coordinates($x + 1, $y + 1),
            new Coordinates($x + 1, $y),
            new Coordinates($x + 1, $y - 1),
            new Coordinates($x, $y - 1),
            new Coordinates($x - 1, $y - 1),
            new Coordinates($x - 1, $y),
            new Coordinates($x - 1, $y + 1),
        ];
    }
}
