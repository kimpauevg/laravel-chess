<?php

declare(strict_types=1);

namespace App\Services\MoveCalculators;

use App\Services\MoveCalculators\Traits\MovesInDirectionUntilObstacleTrait;
use App\DTO\CoordinateModifiers;

class BishopMoveCalculator extends AbstractChessPieceMoveCalculator
{
    use MovesInDirectionUntilObstacleTrait;

    private function getCoordinateModifiers(): array
    {
        return [
            new CoordinateModifiers(CoordinateModifiers::MODIFIER_ADD, CoordinateModifiers::MODIFIER_ADD),
            new CoordinateModifiers(CoordinateModifiers::MODIFIER_ADD, CoordinateModifiers::MODIFIER_SUBTRACT),
            new CoordinateModifiers(CoordinateModifiers::MODIFIER_SUBTRACT, CoordinateModifiers::MODIFIER_ADD),
            new CoordinateModifiers(CoordinateModifiers::MODIFIER_SUBTRACT, CoordinateModifiers::MODIFIER_SUBTRACT),
        ];
    }
}
