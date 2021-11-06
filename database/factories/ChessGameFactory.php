<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ChessGame;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChessGameFactory extends Factory
{
    protected string $model = ChessGame::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word
        ];
    }

    public function hasPieces(ChessGamePieceFactory $factory): self
    {
        return $this->has($factory, 'pieces');
    }
}
