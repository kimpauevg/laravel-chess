<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ChessPieceMoveCalculators;

use App\Models\ChessGame;
use App\Models\Collections\ChessGamePieceCollection;
use Database\Factories\ChessGameFactory;
use Tests\TestCase;

abstract class AbstractChessPieceMoveTest extends TestCase
{
    public function makeGameWithPieces(array $pieces): ChessGame
    {
        $game = ChessGameFactory::new()->make();

        $game->setRelation('pieces', new ChessGamePieceCollection($pieces));

        return $game;
    }
}
