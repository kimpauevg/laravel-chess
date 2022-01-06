<?php

namespace Tests;

use App\DTO\ChessPieceMoves;
use App\DTO\Collections\CoordinatesCollection;
use App\DTO\Coordinates;
use App\Models\Builders\ChessGameBuilder;
use App\Models\ChessGame;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Arr;
use Mockery\MockInterface;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function assertMovesMovementCollectionEquals(
        array                $expected_coordinates_array,
        ChessPieceMoves      $moves
    ): void {
        $actual_coordinates = $moves->movement_coordinates_collection;
        $this->assertCoordinateCollectionEquals($expected_coordinates_array, $actual_coordinates);
    }

    protected function assertCoordinateCollectionEquals(
        array                $expected_coordinates_array,
        CoordinatesCollection $actual_coordinates
    ): void {
        $expected_coordinates_collection = $this->coordinateArrayToCollection($expected_coordinates_array);

        $unexpected_coordinates = $actual_coordinates->subtractCollection($expected_coordinates_collection);

        $this->assertEquals(
            [],
            $this->mapCoordinateCollectionToArray($unexpected_coordinates),
            'Received unexpected coordinates.'
        );

        $unreceived_coordinates = $expected_coordinates_collection->subtractCollection($actual_coordinates);

        $this->assertEquals(
            [],
            $this->mapCoordinateCollectionToArray($unreceived_coordinates),
            'Expected coordinates were not received.'
        );
    }

    protected function assertMovesCapturesCollectionEquals(
        array                $expected_coordinates_array,
        ChessPieceMoves      $moves
    ): void {
        $actual_coordinates = $moves->capture_coordinates_collection;
        $this->assertCoordinateCollectionEquals($expected_coordinates_array, $actual_coordinates);
    }

    private function mapCoordinateCollectionToArray(CoordinatesCollection $collection): array
    {
        return $collection->map(function (Coordinates $coordinates) {
            return "$coordinates->x, $coordinates->y";
        })->all();
    }

    private function coordinateArrayToCollection(array $coordinates): CoordinatesCollection
    {
        $coordinates_collection = collect($coordinates)->map(function (array $one) {
            return new Coordinates(Arr::get($one, 0), Arr::get($one, 1));
        });

        return new CoordinatesCollection($coordinates_collection->all());
    }

    protected function mockGameSearch(ChessGame $game): void
    {
        $this->mock(ChessGameBuilder::class, function (MockInterface $mock) use ($game) {
            $mock->shouldReceive('findOrFail')->andReturn($game);
        });
    }
}
