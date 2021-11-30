<?php

declare(strict_types=1);

namespace App\Http\Formatters;

use App\Services\ValueObjects\Collections\CoordinateCollection;

class CoordinateFormatter
{
    public function formatCollection(CoordinateCollection $collection): array
    {
        $result = [];

        foreach ($collection->all() as $coordinates) {
            $result[] = [
                'x' => $coordinates->x,
                'y' => $coordinates->y,
            ];
        }

        return $result;
    }
}
