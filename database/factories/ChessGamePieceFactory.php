<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ChessGamePiece;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChessGamePieceFactory extends Factory
{
    protected string $model = ChessGamePiece::class;

    public function definition(): array
    {
        return [
            'coordinate_x' => $this->faker->numberBetween(1, 8),
            'coordinate_y' => $this->faker->numberBetween(1, 8),
        ];
    }
}
