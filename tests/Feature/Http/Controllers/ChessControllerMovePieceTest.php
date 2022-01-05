<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Models\ChessGamePiece;
use App\Models\ChessGamePieceMove;
use App\Models\ChessGamePieceMovePromotion;
use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceFactory;
use Tests\Feature\TestCase;

class ChessControllerMovePieceTest extends TestCase
{
    public function testPawnPromotionWithoutName(): void
    {
        ChessGameFactory::new()->id(1)
            ->hasPieces(
                ChessGamePieceFactory::new()->id(1)
                    ->light()->pawn()
                    ->coordinates(1, 7)
            )
            ->create();

        $response = $this->post('/chess-games/1/piece/1/move', [
            'coordinates' => [
                'x' => 1,
                'y' => 8,
            ],
        ]);

        $response->assertSessionHasErrors('promotion_to_piece_name');
    }

    public function testPawnPromotion(): void
    {
        ChessGameFactory::new()->id(1)
            ->hasPieces(
                ChessGamePieceFactory::new()->id(1)
                    ->light()->pawn()
                    ->coordinates(1, 7)
            )
            ->create();

        $response = $this->post('/chess-games/1/piece/1/move', [
            'coordinates' => [
                'x' => 1,
                'y' => 8,
            ],
            'promotion_to_piece_name' => ChessPieceNameDictionary::QUEEN,
        ]);

        $response->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas(ChessGamePiece::TABLE, [
            'id'           => 1,
            'name'         => ChessPieceNameDictionary::QUEEN,
            'coordinate_x' => 1,
            'coordinate_y' => 8,
        ]);

        $this->assertDatabaseHas(ChessGamePieceMove::TABLE, [
            'chess_piece_name'      => ChessPieceNameDictionary::PAWN,
            'previous_coordinate_x' => 1,
            'previous_coordinate_y' => 7,
            'coordinate_x'          => 1,
            'coordinate_y'          => 8,
        ]);

        $this->assertDatabaseHas(ChessGamePieceMovePromotion::TABLE, [
            'to_name' => ChessPieceNameDictionary::QUEEN,
        ]);
    }

    public function testWrongColorPieceMoveTest(): void
    {
        ChessGameFactory::new()->id(1)
            ->hasPieces(
                ChessGamePieceFactory::new()->id(1)
                    ->dark()->pawn()
                    ->coordinates(1, 7)
            )
            ->create();

        $response = $this->post('/chess-games/1/piece/1/move', [
            'coordinates' => [
                'x' => 1,
                'y' => 6,
            ],
        ]);

        $response->assertSessionHasErrors('color');
    }
}
