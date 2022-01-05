<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Models\ChessGamePieceMovePromotion;

class ChessGamePieceMovePromotionFactory extends AbstractFactory
{
    protected $model = ChessGamePieceMovePromotion::class;

    public function definition(): array
    {
        return [
            'to_name' => ChessPieceNameDictionary::QUEEN,
        ];
    }

    public function queen(): static
    {
        return $this->state(['to_name' => ChessPieceNameDictionary::QUEEN]);
    }
}
