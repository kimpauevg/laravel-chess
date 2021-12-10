<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ChessGame;
use App\Models\Collections\ChessGameCollection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method ChessGame|ChessGameCollection make($attributes = [], ?Model $parent = null)
 */
class ChessGameFactory extends AbstractFactory
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

    public function hasMoves(ChessGamePieceMoveFactory $factory): self
    {
        return $this->has($factory, 'moves');
    }
}
