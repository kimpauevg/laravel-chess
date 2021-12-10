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
class ChessGamePieceFactory extends AbstractFactory
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

    public function coordinates(int $coordinate_x, int $coordinate_y): self
    {
        return $this->coordinateX($coordinate_x)->coordinateY($coordinate_y);
    }

    public function coordinateX(int $coordinate): self
    {
        return $this->state(['coordinate_x' => $coordinate]);
    }

    public function coordinateY(int $coordinate): self
    {
        return $this->state(['coordinate_y' => $coordinate]);
    }

    public function light(): self
    {
        return $this->color(ChessPieceDictionary::COLOR_LIGHT);
    }

    public function dark(): self
    {
        return $this->color(ChessPieceDictionary::COLOR_DARK);
    }

    public function color(string $color): self
    {
        return $this->state(['color' => $color]);
    }

    public function pawn(): self
    {
        return $this->name(ChessPieceDictionary::PAWN);
    }

    public function bishop(): self
    {
        return $this->name(ChessPieceDictionary::BISHOP);
    }

    public function name(string $name): self
    {
        return $this->state(['name' => $name]);
    }
}
