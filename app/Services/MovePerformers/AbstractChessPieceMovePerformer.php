<?php

declare(strict_types=1);

namespace App\Services\MovePerformers;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Dictionaries\ChessPieceCoordinates\ChessPieceCoordinateDictionary;
use App\DTO\ChessMoveDataDTO;
use App\DTO\ChessPieceMoves;
use App\DTO\Coordinates;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\ChessGamePieceMove;
use App\Models\ChessGamePieceMovePromotion;
use App\Services\MoveCalculators\ChessPieceMoveCalculatorFactory;
use App\Services\MoveCalculators\PawnMoveCalculator;
use Illuminate\Validation\ValidationException;

abstract class AbstractChessPieceMovePerformer
{
    protected ChessGamePieceMove $current_move;

    public function __construct(
        private ChessPieceMoveCalculatorFactory $chess_piece_move_calculator_factory,
    ) {
    }

    abstract protected function saveMove(ChessGamePieceMove $move): void;
    abstract protected function savePromotionMove(ChessGamePieceMovePromotion $promotion_move): void;
    abstract protected function saveChessPiece(ChessGamePiece $piece): void;

    /**
     * @throws ValidationException
     */
    public function makeMove(ChessMoveDataDTO $move_dto): void
    {
        $piece = $move_dto->piece;
        $game = $move_dto->game;
        $coordinates = $move_dto->move_coordinates;
        $promotion_to_piece_name = $move_dto->promotion_to_piece_name;

        $possible_moves = $this->chess_piece_move_calculator_factory->make($piece)
            ->calculateMovesForPieceInGame($piece, $game);

        $move = new ChessGamePieceMove();

        $move->chess_game_id = $game->id;
        $move->move_index = $game->moves->getLastMoveIndex() + 1;
        $move->chess_piece_name = $piece->name;
        $move->previous_coordinate_x = $piece->coordinate_x;
        $move->previous_coordinate_y = $piece->coordinate_y;
        $move->coordinate_x = $coordinates->x;
        $move->coordinate_y = $coordinates->y;

        $this->current_move = $move;

        $move_is_promotion = $this->shouldBePromoted($piece, $coordinates);

        if ($move_is_promotion && is_null($promotion_to_piece_name)) {
            throw ValidationException::withMessages([
                'promotion_to_piece_name' => ['Pawn requires promotion']
            ]);
        }

        $move_is_movement = $possible_moves->movement_coordinates_collection
            ->whereCoordinates($coordinates->x, $coordinates->y)
            ->exists();

        if ($move_is_movement) {
            $piece->coordinate_x = $coordinates->x;
            $piece->coordinate_y = $coordinates->y;

            $this->saveChessPiece($piece);

            $this->saveMove($move);
        }

        $move_is_capture = $possible_moves->capture_coordinates_collection
            ->whereCoordinates($coordinates->x, $coordinates->y)
            ->exists();

        if ($move_is_capture) {
            $captured_piece = $game->pieces->whereCoordinates($coordinates->x, $coordinates->y)
                ->firstOrFail();

            $captured_piece->is_captured = true;
            $this->saveChessPiece($captured_piece);

            $game->pieces = $game->pieces->filter(function (ChessGamePiece $piece) use ($captured_piece) {
                return $piece->id !== $captured_piece->id;
            });

            $piece->coordinate_x = $coordinates->x;
            $piece->coordinate_y = $coordinates->y;

            $this->saveChessPiece($piece);

            $move->is_capture = true;
            $this->saveMove($move);
        }

        $move_is_en_passant = $possible_moves->en_passant_coordinates_collection
            ->whereCoordinates($coordinates->x, $coordinates->y)
            ->exists();

        if ($move_is_en_passant) {
            /** @var PawnMoveCalculator $move_calculator */
            $move_calculator = app(PawnMoveCalculator::class);
            $y_modifier = $move_calculator->getYCoordinateModifierForPawn($piece);

            $captured_piece = $game->pieces
                ->whereCoordinates($coordinates->x, $coordinates->y - $y_modifier)
                ->firstOrFail();

            $captured_piece->is_captured = true;
            $this->saveChessPiece($captured_piece);

            $piece->coordinate_x = $coordinates->x;
            $piece->coordinate_y = $coordinates->y;

            $this->saveChessPiece($piece);

            $move->is_capture = true;
            $move->is_en_passant = true;

            $this->saveMove($move);
        }

        if ($move_is_promotion) {
            $piece->name = $promotion_to_piece_name;

            $this->saveChessPiece($piece);

            $promotion_move = new ChessGamePieceMovePromotion();

            $promotion_move->move_id = $move->id;
            $promotion_move->to_name = $promotion_to_piece_name;

            $this->saveMove($move);

            $this->savePromotionMove($promotion_move);
        }

        if ($this->isCurrentMoveCheck($game)) {
            $move->is_check = true;
            $this->saveMove($move);
        }

        $game->moves->add($move);

        if (!($move_is_movement || $move_is_capture || $move_is_en_passant)) {
            throw ValidationException::withMessages([
                'coordinates' => ['Move is not allowed'],
            ]);
        }
    }

    private function isCurrentMoveCheck(ChessGame $game): bool
    {
        $prev_move_color = $game->getNextMoveChessPieceColor();

        foreach ($game->pieces->whereColor($prev_move_color)->all() as $piece) {
            $moves = $this->chess_piece_move_calculator_factory->make($piece)
                ->calculateMovesForPieceInGame($piece, $game);

            if ($this->movesForGameHaveKingCapture($moves, $game)) {
                return true;
            }
        }

        return false;
    }

    public function movesForGameHaveKingCapture(ChessPieceMoves $moves, ChessGame $game): bool
    {
        foreach ($moves->capture_coordinates_collection as $coordinates) {
            $piece_on_coordinates = $game->pieces->whereCoordinates($coordinates->x, $coordinates->y)->first();

            if ($piece_on_coordinates?->name === ChessPieceNameDictionary::KING) {
                return true;
            }
        }

        return false;
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
