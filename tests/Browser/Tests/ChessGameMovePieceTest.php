<?php

declare(strict_types=1);

namespace Tests\Browser\Tests;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceFactory;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ChessGamePage;
use Tests\DuskTestCase;

class ChessGameMovePieceTest extends DuskTestCase
{
    public function testPromotion(): void
    {
        ChessGameFactory::new()->id(1)
            ->hasPieces(
                ChessGamePieceFactory::new()->id(1)
                    ->pawn()
                    ->color(ChessPieceColorDictionary::LIGHT)
                    ->coordinates(1, 7)
            )
            ->create();

        $this->browse(function (Browser $browser) {
            $browser->visit(new ChessGamePage(1));

            // select pawn to move
            $pawn_selector = '[data-chess-piece-id="1"]';
            $browser->assertVisible($pawn_selector);
            $browser->click($pawn_selector);

            // move to last row
            $pawn_move_to_coordinates_selector = '[data-coordinate-x="1"][data-coordinate-y="8"]';
            $movement_selector = '.board-square-move'. $pawn_move_to_coordinates_selector;
            $browser->waitFor($movement_selector);
            $browser->click($movement_selector);

            // select to promote pawn to queen
            $promotion_btn = '#select-promotion .promote-pawn[data-name="' . ChessPieceNameDictionary::QUEEN . '"]';
            $browser->waitFor($promotion_btn . ' img');
            $browser->click($promotion_btn);

            // check that table state has been updated
            $promoted_pawn_selector = $pawn_selector . $pawn_move_to_coordinates_selector .
                '[data-chess-piece-name="' . ChessPieceNameDictionary::QUEEN . '"]';
            $browser->waitFor($pawn_selector . $promoted_pawn_selector);
        });
    }
}
