<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ChessPieceMoveResolver;

use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGamePieceCollection;
use App\Services\ChessPieceMoveResolver;
use Database\Factories\ChessGamePieceFactory;
use Tests\TestCase;

abstract class AbstractResolveTest extends TestCase
{
    protected function makeGamePieceWithCoordinates(string $name, string $color, int $x, int $y): ChessGamePiece
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
