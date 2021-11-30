<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGamePieceCollection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method ChessGamePiece|ChessGamePieceCollection make($attributes = [], ?Model $parent = null)
 * @method ChessGamePiece|ChessGamePieceCollection create($attributes = [], ?Model $parent = null)
 */
class ChessGamePieceFactory extends Factory
{
    protected string $model = ChessGamePiece::class;

    public function definition(): array
    {
        return [
            'color'        => ChessPieceDictionary::COLOR_LIGHT,
            'coordinate_x' => $this->faker->numberBetween(1, 8),
            'coordinate_y' => $this->faker->numberBetween(1, 8),
        ];
    }
}
