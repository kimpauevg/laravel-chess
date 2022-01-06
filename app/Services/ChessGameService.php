<?php

declare(strict_types=1);

namespace App\Services;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Dictionaries\ChessPieceCoordinates\ChessPieceCoordinateDictionary;
use App\DTO\ChessPieceMoveData;
use App\DTO\Collections\CoordinatesCollection;
use App\Models\Builders\ChessGameBuilder;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGamePieceCollection;
use App\DTO\ChessPieceMoves;
use App\DTO\Coordinates;
use App\Models\Collections\ChessGamePieceMoveCollection;
use App\Services\MovePerformers\DatabaseChessMovePerformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ChessGameService
{
    public function getChessGames(): LengthAwarePaginator
    {
        return $this->getBuilder()->with(['moves'])->orderBy('created_at')->paginate();
    }

    /**
     * @param int $id
     * @return ChessGame
     * @throws ModelNotFoundException
     */
    public function getGameById(int $id): ChessGame
    {
        return $this->getBuilder()->findOrFail($id);
    }

    public function getGameByIdWithRelations(int $id): ChessGame
    {
        $game = $this->getGameById($id);
        $game->load(['moves.promotion', 'pieces']);

        return $game;
    }

    public function storeGame(array $attributes): ChessGame
    {
        $game = new ChessGame();

        $game->name = Arr::get($attributes, 'name');

        $game->save();

        $this->storeDefaultPiecesForGame($game);

        return $game;
    }

    public function getPossibleMovesForChessPieceById(int $game_id, int $chess_piece_id): ChessPieceMoves
    {
        $game = $this->getGameById($game_id);

        $chess_piece = $game->pieces->findOrFail($chess_piece_id);

        $this->validateChessPieceColorForNextMove($game, $chess_piece);

        /** @var ChessPieceMoveResolver $resolver */
        $resolver = app(ChessPieceMoveResolver::class);

        return $resolver->findMovesOfPieceForGame($chess_piece, $game);
    }

    private function validateChessPieceColorForNextMove(ChessGame $game, ChessGamePiece $piece): void
    {
        $check_color_fn = function ($attribute, $value, $fail) use ($game) {
            $expected_color = $game->getNextMoveChessPieceColor();

            if ($value !== $expected_color) {
                $fail("It's $expected_color turn.");
            }
        };

        Validator::validate(
            ['color' => $piece->color],
            ['color' => $check_color_fn]
        );
    }

    /**
     * @throws ValidationException
     */
    public function makeMove(int $id, int $chess_piece_id, array $data): void
    {
        $game = $this->getGameById($id);

        $chess_piece = $game->pieces->findOrFail($chess_piece_id);

        $this->validateChessPieceColorForNextMove($game, $chess_piece);

        $coordinates = Arr::get($data, 'coordinates');

        $move_coordinates = new Coordinates(
            (int) Arr::get($coordinates, 'x'),
            (int) Arr::get($coordinates, 'y'),
        );

        $promotion_to_piece_name = Arr::get($data, 'promotion_to_piece_name');

        /** @var DatabaseChessMovePerformer $move_performer */
        $move_performer = app(DatabaseChessMovePerformer::class);

        $move_data = new ChessPieceMoveData($game, $chess_piece, $move_coordinates);
        $move_data->promotion_to_piece_name = $promotion_to_piece_name;

        $move_performer->performMove($move_data);
    }

    private function storeDefaultPiecesForGame(ChessGame $game): void
    {
        $pieces_collection = $this->getStartingPiecesCollection();

        foreach ($pieces_collection->all() as $piece) {
            $piece->chess_game_id = $game->id;
            $piece->save();
        }
    }

    private function getStartingPiecesCollection(): ChessGamePieceCollection
    {
        $chess_pieces = [];

        $pieces_by_coordinate_x = [
            1 => ChessPieceNameDictionary::ROOK,
            2 => ChessPieceNameDictionary::KNIGHT,
            3 => ChessPieceNameDictionary::BISHOP,
            4 => ChessPieceNameDictionary::QUEEN,
            5 => ChessPieceNameDictionary::KING,
            6 => ChessPieceNameDictionary::BISHOP,
            7 => ChessPieceNameDictionary::KNIGHT,
            8 => ChessPieceNameDictionary::ROOK,
        ];

        $coordinate_x = ChessPieceCoordinateDictionary::MIN_COORDINATE_X;

        while ($coordinate_x <= ChessPieceCoordinateDictionary::MAX_COORDINATE_X) {
            $chess_piece = new ChessGamePiece();
            $chess_piece->coordinate_x = $coordinate_x;
            $chess_piece->name = Arr::get($pieces_by_coordinate_x, $coordinate_x);

            $light_piece = clone $chess_piece;
            $light_piece->coordinate_y = ChessPieceCoordinateDictionary::LIGHT_PIECE_STARTING_Y_COORDINATE;
            $light_piece->color = ChessPieceColorDictionary::LIGHT;
            $chess_pieces[] = $light_piece;

            $dark_piece = clone $chess_piece;
            $dark_piece->coordinate_y = ChessPieceCoordinateDictionary::DARK_PIECE_STARTING_Y_COORDINATE;
            $dark_piece->color = ChessPieceColorDictionary::DARK;
            $chess_pieces[] = $dark_piece;

            $pawn = clone $chess_piece;
            $pawn->name = ChessPieceNameDictionary::PAWN;

            $light_pawn = clone $pawn;
            $light_pawn->color = ChessPieceColorDictionary::LIGHT;
            $light_pawn->coordinate_y = ChessPieceCoordinateDictionary::LIGHT_PAWN_STARTING_Y_COORDINATE;
            $chess_pieces[] = $light_pawn;

            $dark_pawn = clone $pawn;
            $dark_pawn->color = ChessPieceColorDictionary::DARK;
            $dark_pawn->coordinate_y = ChessPieceCoordinateDictionary::DARK_PAWN_STARTING_Y_COORDINATE;
            $chess_pieces[] = $dark_pawn;

            $coordinate_x++;
        }

        return new ChessGamePieceCollection($chess_pieces);
    }

    private function getBuilder(): ChessGameBuilder
    {
        return app(ChessGameBuilder::class);
    }

    public function cloneGame(ChessGame $game): ChessGame
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

    public function filterCheckCaptureMoves(ChessPieceMoves $moves, ChessGame $game): CoordinatesCollection
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
}
