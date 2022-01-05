<?php

declare(strict_types=1);

namespace App\Http\Formatters;

use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\ChessGamePieceMove;
use App\Models\Collections\ChessGameCollection;
use Illuminate\Support\Collection;

class ChessGameFormatter
{
    public function __construct(
        private ChessPieceNameDictionary $piece_name_dictionary
    ) {
    }

    public function formatCollection(ChessGameCollection $collection): array
    {
        $result = [];

        foreach ($collection->all() as $chess_game) {
            $result[] = [
                'id'   => $chess_game->id,
                'name' => $chess_game->name,
            ];
        }

        return $result;
    }

    public function formatOneWithRelations(ChessGame $game): array
    {
        $result = $this->getGameAttributes($game);

        $result['pieces'] = $this->getChessGamePiecesAttributes($game);
        $result['moves'] = $this->getChessGameMovesAttributes($game);

        return $result;
    }

    private function getGameAttributes(ChessGame $game): array
    {
        return [
            'id'   => $game->id,
            'name' => $game->name,
        ];
    }

    private function getChessGamePiecesAttributes(ChessGame $game): array
    {
        $pieces = [];

        foreach ($game->pieces as $piece) {
            $pieces[] = $this->getChessPieceAttributes($piece);
        }

        return $pieces;
    }

    private function getChessGameMovesAttributes(ChessGame $game): array
    {
        $moves = [];
        $move_pairs = $game->moves->chunk(2);

        $index = 1;

        /** @var Collection $move_pair */
        foreach ($move_pairs as $move_pair) {
            $move = "$index. ";
            $move .= $this->formatMove($move_pair->first());

            if ($move_pair->count() == 2) {
                $move .= ' ' . $this->formatMove($move_pair->last());
            }

            $moves[] = $move;
            $index++;
        }

        return $moves;
    }

    private function formatMove(ChessGamePieceMove $move): string
    {
        if ($this->isKingsideCastling($move)) {
            return '0-0';
        }

        if ($this->isQueensideCastling($move)) {
            return '0-0-0';
        }

        $piece_symbol = $this->getPieceSymbolByName($move->chess_piece_name);
        $starting_coordinates = $this->coordinateXToLetter($move->previous_coordinate_x) . $move->previous_coordinate_y;
        $action_letter = $move->is_capture ? 'x' : '-';
        $end_coordinates = $this->coordinateXToLetter($move->coordinate_x) . $move->coordinate_y;

        $move_as_string = $piece_symbol . $starting_coordinates . $action_letter . $end_coordinates;

        if (!is_null($move->promotion)) {
            $move_as_string .= $this->getPieceSymbolByName($move->promotion->to_name);
        }

        if ($move->is_check && !$move->is_mate) {
            $move_as_string .= '+';
        }

        if ($move->is_mate) {
            $move_as_string .= '#';
        }

        if ($move->is_draw) {
            $move_as_string .= '0.5 - 0.5';
        }

        return $move_as_string;
    }

    private function getPieceSymbolByName(string $name): string
    {
        return $this->piece_name_dictionary->all()->getByName($name)->symbol;
    }

    private function coordinateXToLetter(int $coordinate): string
    {
        $letters = 'abcdefgh';

        return $letters[$coordinate - 1];
    }

    private function isKingsideCastling(ChessGamePieceMove $move): bool
    {
        return $move->chess_piece_name === ChessPieceNameDictionary::KING
            && $move->previous_coordinate_x - $move->coordinate_x  === -2;
    }

    private function isQueensideCastling(ChessGamePieceMove $move): bool
    {
        return $move->chess_piece_name === ChessPieceNameDictionary::KING
            && $move->previous_coordinate_x - $move->coordinate_x === 2;
    }

    private function getChessPieceAttributes(ChessGamePiece $piece): array
    {
        return [
            'id'          => $piece->id,
            'coordinates' => [
                'x' => $piece->coordinate_x,
                'y' => $piece->coordinate_y,
            ],
            'name'  => $piece->name,
            'color' => $piece->color,
        ];
    }
}
