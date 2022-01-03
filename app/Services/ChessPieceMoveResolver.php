<?php

declare(strict_types=1);

namespace App\Services;

use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\DTO\ChessMoveDataDTO;
use App\DTO\ChessPieceMoves;
use App\DTO\Collections\CoordinatesCollection;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGamePieceCollection;
use App\Models\Collections\ChessGamePieceMoveCollection;
use App\Services\MoveCalculators\ChessPieceMoveCalculatorFactory;
use App\Services\MovePerformers\DBLessChessMovePerformer;

class ChessPieceMoveResolver
{
    public function __construct(
        private ChessPieceMoveCalculatorFactory $move_calculator_factory,
        private DBLessChessMovePerformer $dbless_chess_move_performer,
    ) {
    }

    public function findMovesOfPieceForGame(ChessGamePiece $piece, ChessGame $game): ChessPieceMoves
    {
        $moves = $this->move_calculator_factory
            ->make($piece)
            ->calculateMovesForPieceInGame($piece, $game);

        return $this->filterMovesPreventingCheck($moves, $piece, $game);
    }

    private function filterMovesPreventingCheck(
        ChessPieceMoves $moves,
        ChessGamePiece $piece,
        ChessGame $game
    ): ChessPieceMoves {
        $moves->movement_coordinates_collection = $this
            ->filterCoordinatesCollectionNotPreventingCheck($moves->movement_coordinates_collection, $piece, $game);

        $moves->capture_coordinates_collection = $this
            ->filterCoordinatesCollectionNotPreventingCheck($moves->capture_coordinates_collection, $piece, $game);

        $moves->promotion_coordinates_collection = $this
            ->filterCoordinatesCollectionNotPreventingCheck($moves->promotion_coordinates_collection, $piece, $game);

        $moves->en_passant_coordinates_collection = $this
            ->filterCoordinatesCollectionNotPreventingCheck($moves->en_passant_coordinates_collection, $piece, $game);

        $moves->castling_coordinates_collection = $this
            ->filterCoordinatesCollectionNotPreventingCheck($moves->castling_coordinates_collection, $piece, $game);

        return $moves;
    }

    /**
     * Has performance problems.
     * Possible solution: make performers use DTOs instead of models.
     *
     * @param CoordinatesCollection $collection
     * @param ChessGamePiece $piece
     * @param ChessGame $game
     * @return CoordinatesCollection
     */
    private function filterCoordinatesCollectionNotPreventingCheck(
        CoordinatesCollection $collection,
        ChessGamePiece $piece,
        ChessGame $game,
    ): CoordinatesCollection {
        $player_made_check_color = $game->getPreviousMoveChessPieceColor();

        $valid_movements = [];

        foreach ($collection->all() as $move_coordinates) {
            $game_clone = $this->cloneGame($game);

            $piece_clone = $game_clone->pieces->findOrFail($piece->id);

            $move_dto = new ChessMoveDataDTO($game_clone, $piece_clone, $move_coordinates);
            $move_dto->promotion_to_piece_name = ChessPieceNameDictionary::QUEEN;

            $this->dbless_chess_move_performer->makeMove($move_dto);

            if (!$this->canColoredPiecesCaptureKingInGame($player_made_check_color, $game_clone)) {
                $valid_movements[] = $move_coordinates;
            }
        }

        return new CoordinatesCollection($valid_movements);
    }

    private function canColoredPiecesCaptureKingInGame(string $color, ChessGame $game): bool
    {
        foreach ($game->pieces->whereColor($color) as $checking_piece) {
            $checking_moves = $this->move_calculator_factory->make($checking_piece)
                ->calculateMovesForPieceInGame($checking_piece, $game);

            if ($this->dbless_chess_move_performer->movesForGameHaveKingCapture($checking_moves, $game)) {
                return true;
            }
        }

        return false;
    }

    private function cloneGame(ChessGame $game): ChessGame
    {
        $game_clone = clone $game;

        $game_clone->moves = new ChessGamePieceMoveCollection($game_clone->moves->all());

        $game_clone_pieces = [];

        foreach ($game_clone->pieces as $piece) {
            $game_clone_pieces[] = clone $piece;
        }

        $game_clone->pieces = new ChessGamePieceCollection($game_clone_pieces);

        return $game_clone;
    }
}
