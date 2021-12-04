<?php

declare(strict_types=1);

namespace App\Services\ChessPieceMoveCalculators;

use App\Services\ChessPieceMoveCalculators\Traits\MovesInDirectionUntilObstacleTrait;
use App\Services\ValueObjects\CoordinateModifiers;
use JetBrains\PhpStorm\Pure;

class BishopMoveCalculator extends AbstractChessPieceMoveCalculator
{
    use MovesInDirectionUntilObstacleTrait;

    #[Pure] private function getCoordinateModifiers(): array
    {
        return [
            new CoordinateModifiers(CoordinateModifiers::MODIFIER_ADD, CoordinateModifiers::MODIFIER_ADD),
            new CoordinateModifiers(CoordinateModifiers::MODIFIER_ADD, CoordinateModifiers::MODIFIER_SUBTRACT),
            new CoordinateModifiers(CoordinateModifiers::MODIFIER_SUBTRACT, CoordinateModifiers::MODIFIER_ADD),
            new CoordinateModifiers(CoordinateModifiers::MODIFIER_SUBTRACT, CoordinateModifiers::MODIFIER_SUBTRACT),
        ];
    }
}
