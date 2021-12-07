<?php

declare(strict_types=1);

namespace App\ValueObjects;

class CoordinateModifiers
{
    public const MODIFIER_UNCHANGED = 0;
    public const MODIFIER_SUBTRACT = -1;
    public const MODIFIER_ADD = 1;

    public function __construct(
        public int $x_modifier,
        public int $y_modifier,
    ) {
    }
}
