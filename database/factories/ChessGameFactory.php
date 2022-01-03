<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ChessGame;
use App\Models\Collections\ChessGameCollection;
use Illuminate\Database\Eloquent\Model;

/**
 * @method ChessGame|ChessGameCollection make($attributes = [], ?Model $parent = null)
 */
class ChessGameFactory extends AbstractFactory
{
    protected $model = ChessGame::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word
        ];
    }

    public function hasPieces(ChessGamePieceFactory $factory): static
    {
        return $this->has($factory, 'pieces');
    }

    public function hasMoves(ChessGamePieceMoveFactory $factory): static
    {
        return $this->has($factory, 'moves');
    }

    public function name(string $name): static
    {
        return $this->state(['name' => $name]);
    }
}
