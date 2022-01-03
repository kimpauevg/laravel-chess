<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ChessPieceMoveCalculators;

use App\DTO\ChessPieceMoves;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Models\Collections\ChessGamePieceCollection;
use App\Services\MoveCalculators\ChessPieceMoveCalculatorFactory;
use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceFactory;
use Tests\TestCase;

abstract class AbstractChessPieceMoveCalculatorTest extends TestCase
{
    protected function makeGameWithPieces(array $pieces): ChessGame
    {
        $game = ChessGameFactory::new()->make();

        $game->setRelation('pieces', new ChessGamePieceCollection($pieces));

        return $game;
    }

    protected function makeGamePieceWithCoordinates(string $name, string $color, int $x, int $y): ChessGamePiece
    {
        return ChessGamePieceFactory::new([
            'name'         => $name,
            'color'        => $color,
            'coordinate_x' => $x,
            'coordinate_y' => $y,
        ])->make();
    }

    protected function getMovesOnEmptyTableForPiece(ChessGamePiece $piece): ChessPieceMoves
    {
        $game = new ChessGame();

        $game->setRelation('pieces', new ChessGamePieceCollection([$piece]));

        /** @var ChessPieceMoveCalculatorFactory $factory */
        $factory = $this->app->make(ChessPieceMoveCalculatorFactory::class);

        return $factory->make($piece)->calculateMovesForPieceInGame($piece, $game);
    }
}
