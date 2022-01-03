<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGamePieceCollection;
use Illuminate\Database\Eloquent\Model;

/**
 * @method ChessGamePiece|ChessGamePieceCollection make($attributes = [], ?Model $parent = null)
 * @method ChessGamePiece|ChessGamePieceCollection create($attributes = [], ?Model $parent = null)
 */
class ChessGamePieceFactory extends AbstractFactory
{
    protected $model = ChessGamePiece::class;

    public function definition(): array
    {
        return [
            'color'        => ChessPieceColorDictionary::LIGHT,
            'coordinate_x' => $this->faker->numberBetween(1, 8),
            'coordinate_y' => $this->faker->numberBetween(1, 8),
        ];
    }

    public function coordinates(int $coordinate_x, int $coordinate_y): static
    {
        return $this->coordinateX($coordinate_x)->coordinateY($coordinate_y);
    }

    public function coordinateX(int $coordinate): static
    {
        return $this->state(['coordinate_x' => $coordinate]);
    }

    public function coordinateY(int $coordinate): static
    {
        return $this->state(['coordinate_y' => $coordinate]);
    }

    public function light(): static
    {
        return $this->color(ChessPieceColorDictionary::LIGHT);
    }

    public function dark(): static
    {
        return $this->color(ChessPieceColorDictionary::DARK);
    }

    public function color(string $color): static
    {
        return $this->state(['color' => $color]);
    }

    public function pawn(): static
    {
        return $this->name(ChessPieceNameDictionary::PAWN);
    }

    public function bishop(): static
    {
        return $this->name(ChessPieceNameDictionary::BISHOP);
    }

    public function rook(): static
    {
        return $this->name(ChessPieceNameDictionary::ROOK);
    }

    public function knight(): static
    {
        return $this->name(ChessPieceNameDictionary::KNIGHT);
    }

    public function king(): static
    {
        return $this->name(ChessPieceNameDictionary::KING);
    }

    public function name(string $name): static
    {
        return $this->state(['name' => $name]);
    }
}
