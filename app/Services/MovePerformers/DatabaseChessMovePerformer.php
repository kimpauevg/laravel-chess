<?php

declare(strict_types=1);

namespace App\Services\MovePerformers;

use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\DTO\ChessMoveDataDTO;
use App\DTO\ChessPieceMoves;
use App\DTO\Collections\CoordinatesCollection;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\ChessGamePieceMove;
use App\Models\ChessGamePieceMovePromotion;
use App\Services\ChessPieceMoveResolver;
use App\Services\MoveCalculators\ChessPieceMoveCalculatorFactory;

class DatabaseChessMovePerformer extends AbstractChessPieceMovePerformer
{
    public function __construct(
        ChessPieceMoveCalculatorFactory $chess_piece_move_calculator_factory,
        private ChessPieceMoveResolver $chess_piece_move_resolver,
    ) {
        parent::__construct($chess_piece_move_calculator_factory);
    }

    public function performMove(ChessMoveDataDTO $move_dto): void
    {
        parent::performMove($move_dto);

        $move = $move_dto->game->moves->last();

        if ($this->isCurrentMoveCheck($move_dto->game)) {
            $move->is_check = true;
        }

        $next_player_cant_move = !$this->doesNextPlayerHaveMoves($move_dto->game);

        if ($next_player_cant_move && $move->is_check) {
            $move->is_mate = true;
        }

        if ($next_player_cant_move && !$move->is_draw) {
            $move->is_draw = true;
        }

        $this->saveMove($move);
    }

    private function isCurrentMoveCheck(ChessGame $game): bool
    {
        $last_move_piece_color = $game->getLastMoveChessPieceColor();

        foreach ($game->pieces->whereColor($last_move_piece_color)->all() as $piece) {
            $moves = $this->chess_piece_move_calculator_factory->make($piece)
                ->calculateMovesForPieceInGame($piece, $game);

            if ($this->getCheckCaptureMoves($moves, $game)->count() > 0) {
                return true;
            }
        }

        return false;
    }

    private function doesNextPlayerHaveMoves(ChessGame $game): bool
    {
        $checked_piece_color = $game->getNextMoveChessPieceColor();

        foreach ($game->pieces->whereColor($checked_piece_color)->all() as $check_preventing_piece) {
            $moves_preventing_check = $this->chess_piece_move_resolver
                ->findMovesOfPieceForGame($check_preventing_piece, $game);

            $moves_preventing_check_count = $moves_preventing_check->capture_coordinates_collection->count()
                + $moves_preventing_check->movement_coordinates_collection->count()
                + $moves_preventing_check->castling_coordinates_collection->count()
                + $moves_preventing_check->en_passant_coordinates_collection->count()
                + $moves_preventing_check->promotion_coordinates_collection->count();

            if ($moves_preventing_check_count > 0) {
                return true;
            }
        }

        return false;
    }

    private function getCheckCaptureMoves(ChessPieceMoves $moves, ChessGame $game): CoordinatesCollection
    {
        $capture_coordinates_array = [];

        foreach ($moves->capture_coordinates_collection->all() as $capture_coordinates) {
            $piece_on_coordinates = $game->pieces->whereCoordinatesDTO($capture_coordinates)->first();

            if ($piece_on_coordinates?->name === ChessPieceNameDictionary::KING) {
                $capture_coordinates_array[] = $capture_coordinates;
            }
        }

        return new CoordinatesCollection($capture_coordinates_array);
    }

    protected function saveMove(ChessGamePieceMove $move): void
    {
        $move->save();
    }

    protected function saveChessPiece(ChessGamePiece $piece): void
    {
        $piece->save();
    }

    protected function savePromotionMove(ChessGamePieceMovePromotion $promotion_move): void
    {
        $promotion_move->save();
    }
}
