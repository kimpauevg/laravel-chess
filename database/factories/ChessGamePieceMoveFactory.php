<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGamePieceMove;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChessGamePieceMoveFactory extends Factory
{
    protected string $model = ChessGamePieceMove::class;

    public function definition(): array
    {
        $x_coordinates_between = [ChessPieceDictionary::MIN_COORDINATE_X, ChessPieceDictionary::MAX_COORDINATE_X];
        $y_coordinates_between = [ChessPieceDictionary::MIN_COORDINATE_Y, ChessPieceDictionary::MAX_COORDINATE_Y];

        return [
            'previous_coordinate_x' => $this->faker->numberBetween(...$x_coordinates_between),
            'previous_coordinate_y' => $this->faker->numberBetween(...$y_coordinates_between),
            'coordinate_x' => $this->faker->numberBetween(...$x_coordinates_between),
            'coordinate_y' => $this->faker->numberBetween(...$y_coordinates_between),
            'chess_piece_name' => ChessPieceDictionary::QUEEN,
            'move_index' => 1
        ];
    }

    public function pawn(): self
    {
        return $this->state(['chess_piece_name' => ChessPieceDictionary::PAWN]);
    }

    public function previousCoordinates(int $x, int $y): self
    {
        return $this->state([
            'previous_coordinate_x' => $x,
            'previous_coordinate_y' => $y,
        ]);
    }

    public function newCoordinates(int $x, int $y): self
    {
        return $this->state([
            'coordinate_x' => $x,
            'coordinate_y' => $y,
        ]);
    }
}
