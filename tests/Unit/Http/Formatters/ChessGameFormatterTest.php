<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Formatters;

use App\Http\Formatters\ChessGameFormatter;
use App\Models\Collections\ChessGameCollection;
use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceMoveFactory;
use Database\Factories\ChessGamePieceMovePromotionFactory;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ChessGameFormatterTest extends TestCase
{
    public function testFormatStatusStarted(): void
    {
        $game = ChessGameFactory::new()->make();

        $result = $this->getFormatter()->formatCollection(new ChessGameCollection([$game]));

        $this->assertEquals('Started', Arr::get($result, '0.status'));
    }

    public function testFormatStatusDraw(): void
    {
        $game = ChessGameFactory::new()->make();

        $move = ChessGamePieceMoveFactory::new()->draw()->make();

        $game->moves->add($move);

        $result = $this->getFormatter()->formatCollection(new ChessGameCollection([$game]));

        $this->assertEquals('Draw', Arr::get($result, '0.status'));
    }

    public function testFormatStatusMate(): void
    {
        $game = ChessGameFactory::new()->make();

        $move = ChessGamePieceMoveFactory::new()->moveIndex(1)->mate()->make();

        $game->moves->add($move);

        $result = $this->getFormatter()->formatCollection(new ChessGameCollection([$game]));

        $this->assertEquals('Light Mate', Arr::get($result, '0.status'));
    }

    public function testFormatStatusCheck(): void
    {
        $game = ChessGameFactory::new()->make();

        $move = ChessGamePieceMoveFactory::new()->moveIndex(2)->check()->make();

        $game->moves->add($move);

        $result = $this->getFormatter()->formatCollection(new ChessGameCollection([$game]));

        $this->assertEquals('Dark Check', Arr::get($result, '0.status'));
    }

    public function testFormatStatusInProgress(): void
    {
        $game = ChessGameFactory::new()->make();

        $move = ChessGamePieceMoveFactory::new()->make();

        $game->moves->add($move);

        $result = $this->getFormatter()->formatCollection(new ChessGameCollection([$game]));

        $this->assertEquals('In progress', Arr::get($result, '0.status'));
    }

    public function testFormatMovesMovement(): void
    {
        $game = ChessGameFactory::new()->make();

        $movement = ChessGamePieceMoveFactory::new()
            ->king()
            ->previousCoordinates(1, 1)
            ->newCoordinates(2, 2)
            ->make();

        $game->moves->add($movement);

        $result = $this->getFormatter()->formatOneWithRelations($game);

        $this->assertEquals('1. Ka1-b2', Arr::get($result, 'moves.0'));
    }

    public function testFormatMovesCapture(): void
    {
        $game = ChessGameFactory::new()->make();

        $movement = ChessGamePieceMoveFactory::new()
            ->previousCoordinates(1, 1)
            ->newCoordinates(2, 2)
            ->capture()
            ->make();

        $game->moves->add($movement);

        $result = $this->getFormatter()->formatOneWithRelations($game);

        $this->assertStringContainsString('a1xb2', Arr::get($result, 'moves.0'));
    }

    public function testFormatMovesCastlings(): void
    {
        $game = ChessGameFactory::new()->make();

        $kingside_castling = ChessGamePieceMoveFactory::new()
            ->king()
            ->previousCoordinates(5, 1)
            ->newCoordinates(7, 1)
            ->make();

        $queenside_castling = ChessGamePieceMoveFactory::new()
            ->king()
            ->previousCoordinates(5, 8)
            ->newCoordinates(3, 8)
            ->make();

        $game->moves->add($kingside_castling)->add($queenside_castling);

        $result = $this->getFormatter()->formatOneWithRelations($game);

        $this->assertStringContainsString('0-0 0-0-0', Arr::get($result, 'moves.0'));
    }

    public function testFormatMovesPromotion(): void
    {
        $game = ChessGameFactory::new()->make();

        $move_promotion = ChessGamePieceMoveFactory::new()
            ->pawn()
            ->previousCoordinates(1, 7)
            ->newCoordinates(1, 8)
            ->make();

        $promotion = ChessGamePieceMovePromotionFactory::new()
            ->queen()
            ->make();

        $move_promotion->promotion = $promotion;

        $game->moves->add($move_promotion);

        $result = $this->getFormatter()->formatOneWithRelations($game);

        $this->assertStringContainsString('a7-a8Q', Arr::get($result, 'moves.0'));
    }

    public function testFormatMovesDraw(): void
    {
        $game = ChessGameFactory::new()->make();

        $draw = ChessGamePieceMoveFactory::new()
            ->draw()
            ->make();

        $game->moves->add($draw);

        $result = $this->getFormatter()->formatOneWithRelations($game);

        $this->assertStringContainsString('0.5 - 0.5', Arr::get($result, 'moves.0'));
    }

    public function testFormatMovesCheck(): void
    {
        $game = ChessGameFactory::new()->make();

        $check = ChessGamePieceMoveFactory::new()
            ->check()
            ->make();

        $game->moves->add($check);

        $result = $this->getFormatter()->formatOneWithRelations($game);

        $this->assertStringContainsString('+', Arr::get($result, 'moves.0'));
    }

    public function testFormatMovesMate(): void
    {
        $game = ChessGameFactory::new()->make();

        $mate = ChessGamePieceMoveFactory::new()
            ->mate()
            ->make();

        $game->moves->add($mate);

        $result = $this->getFormatter()->formatOneWithRelations($game);

        $this->assertStringContainsString('#', Arr::get($result, 'moves.0'));
    }

    private function getFormatter(): ChessGameFormatter
    {
        return $this->app->make(ChessGameFormatter::class);
    }
}
