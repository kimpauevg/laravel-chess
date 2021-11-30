<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ChessPieceMoveResolver;

use App\Models\ChessGamePiece;
use App\Services\ValueObjects\Collections\CoordinateCollection;
use App\Services\ValueObjects\Coordinates;
use Database\Factories\ChessGamePieceFactory;
use Tests\TestCase;

abstract class AbstractResolveTest extends TestCase
{
    protected function assertCoordinatesCollectionEquals(
        array $expected_coordinates,
        CoordinateCollection $actual_coordinates
    ): void {
        $actual_coordinates = $actual_coordinates->map(function (Coordinates $coordinate) {
            return [$coordinate->x, $coordinate->y];
        })->all();

        $this->assertEquals($expected_coordinates, $actual_coordinates);
    }

    protected function makePieceWithCoordinates(string $name, string $color, int $x, int $y): ChessGamePiece
    {
        return ChessGamePieceFactory::new([
            'name'         => $name,
            'color'        => $color,
            'coordinate_x' => $x,
            'coordinate_y' => $y,
        ])->make();
    }
}
