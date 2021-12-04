<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ChessPieceMoveResolver;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGamePieceCollection;
use App\Services\ChessPieceMoveResolver;
use App\Services\ValueObjects\Collections\CoordinateCollection;
use App\Services\ValueObjects\Coordinates;
use Database\Factories\ChessGamePieceFactory;
use Illuminate\Support\Arr;
use Tests\TestCase;

abstract class AbstractResolveTest extends TestCase
{
    protected function assertCoordinatesCollectionEquals(
        array                $expected_coordinates_array,
        CoordinateCollection $actual_coordinates
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

    public function mapCoordinateCollectionToArray(CoordinateCollection $collection): array
    {
        return $collection->map(function (Coordinates $coordinates) {
            return "$coordinates->x, $coordinates->y";
        })->all();
    }

    public function coordinateArrayToCollection(array $coordinates): CoordinateCollection
    {
        $coordinates_collection = collect($coordinates)->map(function (array $one) {
            return new Coordinates(Arr::get($one, 0), Arr::get($one, 1));
        });

        return new CoordinateCollection($coordinates_collection->all());
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

    public function makeResolverForEmptyTable(ChessGamePiece $piece): ChessPieceMoveResolver
    {
        $game = new ChessGame();

        $game->setRelation('pieces', new ChessGamePieceCollection([$piece]));

        return new ChessPieceMoveResolver($piece, $game);
    }
}
