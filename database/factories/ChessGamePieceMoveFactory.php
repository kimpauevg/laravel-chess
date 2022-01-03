<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Dictionaries\ChessPieceCoordinates\ChessPieceCoordinateDictionary;
use App\Models\ChessGamePieceMove;

class ChessGamePieceMoveFactory extends AbstractFactory
{
    protected $model = ChessGamePieceMove::class;

    public function definition(): array
    {
        $x_coordinates_between = [ChessPieceCoordinateDictionary::MIN_COORDINATE_X, ChessPieceCoordinateDictionary::MAX_COORDINATE_X];
        $y_coordinates_between = [ChessPieceCoordinateDictionary::MIN_COORDINATE_Y, ChessPieceCoordinateDictionary::MAX_COORDINATE_Y];

        return [
            'previous_coordinate_x' => $this->faker->numberBetween(...$x_coordinates_between),
            'previous_coordinate_y' => $this->faker->numberBetween(...$y_coordinates_between),
            'coordinate_x'          => $this->faker->numberBetween(...$x_coordinates_between),
            'coordinate_y'          => $this->faker->numberBetween(...$y_coordinates_between),
            'chess_piece_name'      => ChessPieceNameDictionary::QUEEN,
            'move_index'            => 1,
            'is_check'              => true,
        ];
    }

    public function pawn(): static
    {
        return $this->name(ChessPieceNameDictionary::PAWN);
    }

    public function knight(): static
    {
        return $this->name(ChessPieceNameDictionary::KNIGHT);
    }

    public function check(): static
    {
        return $this->state(['is_check' => true]);
    }

    public function name(string $name): static
    {
        return $this->state(['chess_piece_name' => $name]);
    }

    public function moveIndex(int $index): static
    {
        return $this->state(['move_index' => $index]);
    }

    public function previousCoordinates(int $x, int $y): static
    {
        return $this->state([
            'previous_coordinate_x' => $x,
            'previous_coordinate_y' => $y,
        ]);
    }

    public function newCoordinates(int $x, int $y): static
    {
        return $this->state([
            'coordinate_x' => $x,
            'coordinate_y' => $y,
        ]);
    }
}
