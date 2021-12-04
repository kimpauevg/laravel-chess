<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Services\ChessPieceMoveCalculators\Traits\MovesInDirectionUntilObstacleTrait;
use App\Services\ValueObjects\CoordinateModifiers;
use JetBrains\PhpStorm\Pure;

class RookMoveCalculator extends AbstractChessPieceMoveCalculator
{
    use MovesInDirectionUntilObstacleTrait;

    #[Pure] private function getCoordinateModifiers(): array
    {
        return [
            new CoordinateModifiers(CoordinateModifiers::MODIFIER_UNCHANGED, CoordinateModifiers::MODIFIER_ADD),
            new CoordinateModifiers(CoordinateModifiers::MODIFIER_UNCHANGED, CoordinateModifiers::MODIFIER_SUBTRACT),
            new CoordinateModifiers(CoordinateModifiers::MODIFIER_ADD, CoordinateModifiers::MODIFIER_UNCHANGED),
            new CoordinateModifiers(CoordinateModifiers::MODIFIER_SUBTRACT, CoordinateModifiers::MODIFIER_UNCHANGED),
        ];
    }
}
