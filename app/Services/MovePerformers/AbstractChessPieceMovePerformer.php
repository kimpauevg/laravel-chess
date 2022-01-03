<?php

declare(strict_types=1);

namespace App\Services\MovePerformers;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Dictionaries\ChessPieceCoordinates\ChessPieceCoordinateDictionary;
use App\DTO\ChessMoveDataDTO;
use App\DTO\Coordinates;
use App\Models\ChessGamePiece;
use App\Models\ChessGamePieceMove;
use App\Models\ChessGamePieceMovePromotion;
use App\Services\MoveCalculators\ChessPieceMoveCalculatorFactory;
use Illuminate\Validation\ValidationException;

abstract class AbstractChessPieceMovePerformer
{
    public function __construct(
        protected ChessPieceMoveCalculatorFactory $chess_piece_move_calculator_factory,
    ) {
    }

    abstract protected function saveMove(ChessGamePieceMove $move): void;
    abstract protected function savePromotionMove(ChessGamePieceMovePromotion $promotion_move): void;
    abstract protected function saveChessPiece(ChessGamePiece $piece): void;

    /**
     * @throws ValidationException
     */
    public function performMove(ChessMoveDataDTO $move_dto): void
    {
        $move = $this->tryToPerformMove($move_dto);

        $move_dto->game->moves->add($move);
    }

    private function tryToPerformMove(ChessMoveDataDTO $move_dto): ChessGamePieceMove
    {
        $possible_moves = $this->chess_piece_move_calculator_factory->make($move_dto->piece)
            ->calculateMovesForPieceInGame($move_dto->piece, $move_dto->game);

        $move_is_movement = $possible_moves->movement_coordinates_collection
            ->whereCoordinatesDTO($move_dto->move_coordinates)
            ->exists();

        if ($move_is_movement) {
            return $this->performMovement($move_dto);
        }

        $move_is_capture = $possible_moves->capture_coordinates_collection
            ->whereCoordinatesDTO($move_dto->move_coordinates)
            ->exists();

        if ($move_is_capture) {
            return $this->performCapture($move_dto);
        }

        $move_is_en_passant = $possible_moves->en_passant_coordinates_collection
            ->whereCoordinatesDTO($move_dto->move_coordinates)
            ->exists();

        if ($move_is_en_passant) {
            return $this->performEnPassant($move_dto);
        }

        $move_is_promotion = $this->shouldBePromoted($move_dto->piece, $move_dto->move_coordinates);

        if ($move_is_promotion && is_null($move_dto->promotion_to_piece_name)) {
            throw ValidationException::withMessages([
                'promotion_to_piece_name' => ['Pawn requires promotion']
            ]);
        }

        if ($move_is_promotion) {
            return $this->performPromotion($move_dto);
        }

        throw ValidationException::withMessages([
            'coordinates' => ['Move is not allowed'],
        ]);
    }

    private function performMovement(ChessMoveDataDTO $move_dto): ChessGamePieceMove
    {
        $move_dto->piece->coordinate_x = $move_dto->move_coordinates->x;
        $move_dto->piece->coordinate_y = $move_dto->move_coordinates->y;

        $this->saveChessPiece($move_dto->piece);

        $move = $this->getChessGamePieceMove($move_dto);

        $this->saveMove($move);

        return $move;
    }

    private function performCapture(ChessMoveDataDTO $move_dto): ChessGamePieceMove
    {
        $captured_piece = $move_dto->game->pieces
            ->whereCoordinates($move_dto->move_coordinates->x, $move_dto->move_coordinates->y)
            ->firstOrFail();

        $captured_piece->is_captured = true;

        $this->saveChessPiece($captured_piece);

        $move_dto->game->pieces = $move_dto->game->pieces->filter(function (ChessGamePiece $piece) use ($captured_piece) {
            return $piece->id !== $captured_piece->id;
        });

        $move = $this->performMovement($move_dto);

        $move->is_capture = true;

        $this->saveMove($move);

        return $move;
    }

    private function performEnPassant(ChessMoveDataDTO $move_dto): ChessGamePieceMove
    {
        $move_coordinates = $move_dto->move_coordinates;

        $capture_coordinates = new Coordinates(
            $move_dto->move_coordinates->x,
            $move_dto->piece->coordinate_y,
        );

        $move_dto->move_coordinates = $capture_coordinates;

        $move = $this->performCapture($move_dto);

        $move_dto->piece->coordinate_x = $move_coordinates->x;
        $move_dto->piece->coordinate_y = $move_coordinates->y;

        $this->saveChessPiece($move_dto->piece);

        $move->coordinate_x = $move_coordinates->x;
        $move->coordinate_y = $move_coordinates->y;

        $move->is_en_passant = true;

        $this->saveMove($move);

        return $move;
    }

    private function performPromotion(ChessMoveDataDTO $move_dto): ChessGamePieceMove
    {
        $move = $this->performMovement($move_dto);

        $move_dto->piece->name = $move_dto->promotion_to_piece_name;

        $this->saveChessPiece($move_dto->piece);

        $promotion_move = new ChessGamePieceMovePromotion();

        $promotion_move->move_id = $move->id;
        $promotion_move->to_name = $move_dto->promotion_to_piece_name;

        $this->savePromotionMove($promotion_move);

        return $move;
    }

    private function getChessGamePieceMove(ChessMoveDataDTO $move_dto): ChessGamePieceMove
    {
        $move = new ChessGamePieceMove();

        $move->chess_game_id = $move_dto->game->id;
        $move->move_index = $move_dto->game->moves->getLastMoveIndex() + 1;
        $move->chess_piece_name = $move_dto->piece->name;
        $move->previous_coordinate_x = $move_dto->piece->coordinate_x;
        $move->previous_coordinate_y = $move_dto->piece->coordinate_y;
        $move->coordinate_x = $move_dto->move_coordinates->x;
        $move->coordinate_y = $move_dto->move_coordinates->y;

        return $move;
    }

    private function shouldBePromoted(ChessGamePiece $piece, Coordinates $new_coordinates): bool
    {
        if ($piece->name !== ChessPieceNameDictionary::PAWN) {
            return false;
        }

        $dark_pawn_reached_light_starting_position = $piece->color === ChessPieceColorDictionary::DARK
            && $new_coordinates->y === ChessPieceCoordinateDictionary::LIGHT_PIECE_STARTING_Y_COORDINATE;

        $light_pawn_reached_dark_starting_position = $piece->color === ChessPieceColorDictionary::LIGHT
            && $new_coordinates->y === ChessPieceCoordinateDictionary::DARK_PIECE_STARTING_Y_COORDINATE;

        if (!($dark_pawn_reached_light_starting_position || $light_pawn_reached_dark_starting_position)) {
            return false;
        }

        return true;
    }
}
