<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Dictionaries\ChessPieceColors\ChessPieceColorDictionary;
use App\Dictionaries\ChessPieceNames\ChessPieceNameDictionary;
use App\Models\Builders\ChessGamePieceBuilder;
use App\Models\ChessGame;
use Tests\Feature\TestCase;

class ChessControllerStoreTest extends TestCase
{
    public function testStore(): void
    {
        $response = $this->post('/chess-games', ['name' => 'Test Game']);

        $response->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas(ChessGame::TABLE, [
            'name' => 'Test Game',
        ]);

        /** @var ChessGamePieceBuilder $pieces_query */
        $pieces_query = app(ChessGamePieceBuilder::class);
        $pieces = $pieces_query->get();

        $light_pawns = $pieces->whereName(ChessPieceNameDictionary::PAWN)
            ->whereCoordinateY(2)
            ->whereColor(ChessPieceColorDictionary::LIGHT)
            ->count();
        $this->assertEquals(8, $light_pawns);

        $dark_pawns = $pieces->whereName(ChessPieceNameDictionary::PAWN)
            ->whereCoordinateY(7)
            ->whereColor(ChessPieceColorDictionary::DARK)
            ->count();
        $this->assertEquals(8, $dark_pawns);

        $correct_name_order = [
            ChessPieceNameDictionary::ROOK,
            ChessPieceNameDictionary::KNIGHT,
            ChessPieceNameDictionary::BISHOP,
            ChessPieceNameDictionary::QUEEN,
            ChessPieceNameDictionary::KING,
            ChessPieceNameDictionary::BISHOP,
            ChessPieceNameDictionary::KNIGHT,
            ChessPieceNameDictionary::ROOK,
        ];

        $light_pieces_names_order = $pieces->whereCoordinateY(1)
            ->whereColor(ChessPieceColorDictionary::LIGHT)
            ->sortBy('coordinate_x')
            ->pluck('name')
            ->toArray();

        $dark_pieces_names_order = $pieces->whereCoordinateY(8)
            ->whereColor(ChessPieceColorDictionary::DARK)
            ->sortBy('coordinate_x')
            ->pluck('name')
            ->toArray();

        $this->assertEquals($correct_name_order, $light_pieces_names_order);
        $this->assertEquals($correct_name_order, $dark_pieces_names_order);
    }
}
