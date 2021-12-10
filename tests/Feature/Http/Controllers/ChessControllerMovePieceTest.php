<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGamePiece;
use App\Models\ChessGamePieceMove;
use App\Models\ChessGamePieceMovePromotion;
use Database\Factories\ChessGameFactory;
use Database\Factories\ChessGamePieceFactory;
use Database\Factories\ChessGamePieceMoveFactory;
use Tests\Feature\TestCase;

class ChessControllerMovePieceTest extends TestCase
{
    public function testBishopMove(): void
    {
        ChessGameFactory::new()->id(1)
            ->hasPieces(
                ChessGamePieceFactory::new()
                    ->id(1)
                    ->light()->bishop()
                    ->coordinates(1, 1)
            )
            ->create();

        $response = $this->post('/chess-games/1/piece/1/move', [
            'coordinates' => [
                'x' => 8,
                'y' => 8,
            ],
        ]);

        $response->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas(ChessGamePiece::TABLE, [
            'id'           => 1,
            'coordinate_x' => 8,
            'coordinate_y' => 8,
        ]);
    }

    public function testBishopCapture(): void
    {
        ChessGameFactory::new()->id(1)
            ->hasPieces(
                ChessGamePieceFactory::new()
                    ->id(1)
                    ->light()->bishop()
                    ->coordinates(1, 1)
            )
            ->hasPieces(
                ChessGamePieceFactory::new()
                    ->id(2)
                    ->dark()->bishop()
                    ->coordinates(8, 8)
            )
            ->create();

        $response = $this->post('/chess-games/1/piece/1/move', [
            'coordinates' => [
                'x' => 8,
                'y' => 8,
            ],
        ]);

        $response->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas(ChessGamePiece::TABLE, [
            'id'           => 1,
            'coordinate_x' => 8,
            'coordinate_y' => 8,
        ]);

        $this->assertDatabaseHas(ChessGamePiece::TABLE, [
            'id' => 2,
            'is_captured' => true,
        ]);

        $this->assertDatabaseHas(ChessGamePieceMove::TABLE, [
            'previous_coordinate_x' => 1,
            'previous_coordinate_y' => 1,
            'coordinate_x' => 8,
            'coordinate_y' => 8,
            'is_capture' => true
        ]);
    }

    public function testPawnEnPassant(): void
    {
        ChessGameFactory::new(['id' => 1])
            ->hasPieces(
                ChessGamePieceFactory::new(['id' => 1])
                    ->light()->pawn()
                    ->coordinates(1, 5)
            )
            ->hasPieces(
                ChessGamePieceFactory::new(['id' => 2])
                    ->dark()->pawn()
                    ->coordinates(2, 5)
            )
            ->hasMoves(
                ChessGamePieceMoveFactory::new()
                    ->pawn()
                    ->previousCoordinates(2, 7)
                    ->newCoordinates(2, 5)
            )
            ->create();

        $response = $this->post('/chess-games/1/piece/1/move', [
            'coordinates' => [
                'x' => 2,
                'y' => 6,
            ],
        ]);

        $response->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas(ChessGamePiece::TABLE, [
            'id'           => 1,
            'coordinate_x' => 2,
            'coordinate_y' => 6,
        ]);

        $this->assertDatabaseHas(ChessGamePiece::TABLE, [
            'id' => 2,
            'is_captured' => true,
        ]);

        $this->assertDatabaseHas(ChessGamePieceMove::TABLE, [
            'chess_piece_name' => ChessPieceDictionary::PAWN,
            'previous_coordinate_x' => 1,
            'previous_coordinate_y' => 5,
            'coordinate_x' => 2,
            'coordinate_y' => 6,
            'is_en_passant' => true,
        ]);
    }

    public function testPawnPromotion(): void
    {
        ChessGameFactory::new(['id' => 1])
            ->hasPieces(
                ChessGamePieceFactory::new(['id' => 1])
                    ->light()->pawn()
                    ->coordinates(1, 7)
            )
            ->create();

        $response = $this->post('/chess-games/1/piece/1/move', [
            'coordinates' => [
                'x' => 1,
                'y' => 8,
            ],
            'promotion_to_piece_name' => ChessPieceDictionary::QUEEN,
        ]);

        $response->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas(ChessGamePiece::TABLE, [
            'id' => 1,
            'name' => ChessPieceDictionary::QUEEN,
            'coordinate_x' => 1,
            'coordinate_y' => 8,
        ]);

        $this->assertDatabaseHas(ChessGamePieceMove::TABLE, [
            'chess_piece_name' => ChessPieceDictionary::PAWN,
            'previous_coordinate_x' => 1,
            'previous_coordinate_y' => 7,
            'coordinate_x' => 1,
            'coordinate_y' => 8,
        ]);

        $this->assertDatabaseHas(ChessGamePieceMovePromotion::TABLE, [
            'to_name' => ChessPieceDictionary::QUEEN,
        ]);
    }
}
