<?php

declare(strict_types=1);

namespace App\Services\MovePerformers;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Dictionaries\ChessPieceCoordinates\ChessPieceCoordinateDictionary;
use App\DTO\ChessPieceMoveData;
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
    public function performMove(ChessPieceMoveData $move_data): void
    {
        $move = $this->tryToPerformMove($move_data);

        $move_data->game->moves->add($move);
    }

    private function tryToPerformMove(ChessPieceMoveData $move_data): ChessGamePieceMove
    {
        $possible_moves = $this->chess_piece_move_calculator_factory->make($move_data->piece)
            ->calculateMovesForPieceInGame($move_data->piece, $move_data->game);

        $move_is_promotion = $this->shouldBePromoted($move_data->piece, $move_data->move_coordinates);

        if ($move_is_promotion && is_null($move_data->promotion_to_piece_name)) {
            throw ValidationException::withMessages([
                'promotion_to_piece_name' => ['Pawn requires promotion']
            ]);
        }

        if ($move_is_promotion) {
            return $this->performPromotion($move_data);
        }

        $move_is_movement = $possible_moves->movement_coordinates_collection
            ->whereCoordinatesDTO($move_data->move_coordinates)
            ->exists();

        if ($move_is_movement) {
            return $this->performMovement($move_data);
        }

        $move_is_capture = $possible_moves->capture_coordinates_collection
            ->whereCoordinatesDTO($move_data->move_coordinates)
            ->exists();

        if ($move_is_capture) {
            return $this->performCapture($move_data);
        }

        $move_is_en_passant = $possible_moves->en_passant_coordinates_collection
            ->whereCoordinatesDTO($move_data->move_coordinates)
            ->exists();

        if ($move_is_en_passant) {
            return $this->performEnPassant($move_data);
        }

        $move_is_castling = $possible_moves->castling_coordinates_collection
            ->whereCoordinatesDTO($move_data->move_coordinates)
            ->exists();

        if ($move_is_castling) {
            return $this->performCastling($move_data);
        }

        throw ValidationException::withMessages([
            'coordinates' => ['Move is not allowed'],
        ]);
    }

    private function performMovement(ChessPieceMoveData $move_data): ChessGamePieceMove
    {
        $move = $this->getChessGamePieceMove($move_data);

        $move_data->piece->coordinate_x = $move_data->move_coordinates->x;
        $move_data->piece->coordinate_y = $move_data->move_coordinates->y;

        $this->saveChessPiece($move_data->piece);

        $this->saveMove($move);

        return $move;
    }

    private function performCapture(ChessPieceMoveData $move_data): ChessGamePieceMove
    {
        $captured_piece = $move_data->game->pieces
            ->whereCoordinates($move_data->move_coordinates->x, $move_data->move_coordinates->y)
            ->firstOrFail();

        $captured_piece->is_captured = true;

        $this->saveChessPiece($captured_piece);

        $move_data->game->pieces = $move_data->game->pieces
            ->filter(function (ChessGamePiece $piece) use ($captured_piece) {
                return $piece->id !== $captured_piece->id;
            });

        $move = $this->performMovement($move_data);

        $move->is_capture = true;

        $this->saveMove($move);

        return $move;
    }

    private function performEnPassant(ChessPieceMoveData $move_data): ChessGamePieceMove
    {
        $move_coordinates = $move_data->move_coordinates;

        $capture_coordinates = new Coordinates(
            $move_data->move_coordinates->x,
            $move_data->piece->coordinate_y,
        );

        $move_data->move_coordinates = $capture_coordinates;

        $move = $this->performCapture($move_data);

        $move_data->piece->coordinate_x = $move_coordinates->x;
        $move_data->piece->coordinate_y = $move_coordinates->y;

        $this->saveChessPiece($move_data->piece);

        $move->coordinate_x = $move_coordinates->x;
        $move->coordinate_y = $move_coordinates->y;

        $move->is_en_passant = true;

        $this->saveMove($move);

        return $move;
    }

    private function performPromotion(ChessPieceMoveData $move_data): ChessGamePieceMove
    {
        $move = $this->performMovement($move_data);

        $move_data->piece->name = $move_data->promotion_to_piece_name;

        $this->saveChessPiece($move_data->piece);

        $promotion_move = new ChessGamePieceMovePromotion();

        $promotion_move->move_id = $move->id;
        $promotion_move->to_name = $move_data->promotion_to_piece_name;

        $this->savePromotionMove($promotion_move);

        $move->setRelation('promotion', $promotion_move);

        return $move;
    }

    private function performCastling(ChessPieceMoveData $move_data): ChessGamePieceMove
    {
        $coordinate_x_of_rook_to_move = ChessPieceCoordinateDictionary::MIN_COORDINATE_X;
        $coordinate_x_to_move_rook_to = $move_data->piece->coordinate_x - 1;

        if ($move_data->move_coordinates->x > $move_data->piece->coordinate_x) {
            $coordinate_x_of_rook_to_move = ChessPieceCoordinateDictionary::MAX_COORDINATE_X;
            $coordinate_x_to_move_rook_to = $move_data->piece->coordinate_x + 1;
        }

        $rook_to_move = $move_data->game->pieces
            ->whereCoordinates($coordinate_x_of_rook_to_move, $move_data->piece->coordinate_y)
            ->firstOrFail();

        $rook_to_move->coordinate_x = $coordinate_x_to_move_rook_to;

        $this->saveChessPiece($rook_to_move);

        return $this->performMovement($move_data);
    }

    private function getChessGamePieceMove(ChessPieceMoveData $move_data): ChessGamePieceMove
    {
        $move = new ChessGamePieceMove();

        $move->chess_game_id = $move_data->game->id;
        $move->move_index = $move_data->game->moves->getLastMoveIndex() + 1;
        $move->chess_piece_name = $move_data->piece->name;
        $move->previous_coordinate_x = $move_data->piece->coordinate_x;
        $move->previous_coordinate_y = $move_data->piece->coordinate_y;
        $move->coordinate_x = $move_data->move_coordinates->x;
        $move->coordinate_y = $move_data->move_coordinates->y;

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
