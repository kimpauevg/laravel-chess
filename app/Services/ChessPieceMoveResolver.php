<?php

declare(strict_types=1);

namespace App\Services;

use App\Dictionaries\ChessPieces\ChessPieceDictionary;
use App\Models\ChessGame;
use App\Models\ChessGamePiece;
use App\Services\ChessPieceMoveCalculators\AbstractChessPieceMoveCalculator;
use App\Services\ChessPieceMoveCalculators\BishopMoveCalculator;
use App\Services\ChessPieceMoveCalculators\KingMoveCalculator;
use App\Services\ChessPieceMoveCalculators\KnightMoveCalculator;
use App\Services\ChessPieceMoveCalculators\PawnMoveCalculator;
use App\Services\ChessPieceMoveCalculators\QueenMoveCalculator;
use App\Services\ChessPieceMoveCalculators\RookMoveCalculator;
use App\Services\ValueObjects\Collections\CoordinateCollection;

class ChessPieceMoveResolver
{
    private ChessGamePiece $piece;
    private ChessGame $game;

    public function __construct(ChessGamePiece $piece, ChessGame $game)
    {
        $this->piece = $piece;
        $this->game = $game;
    }

    public function getPossibleMovesCoordinates(): CoordinateCollection
    {
        $piece = $this->piece;

        $calculator = $this->getCalculatorByPieceName($piece->name);

        if ($calculator === null) {
            return new CoordinateCollection();
        }

        return $calculator->calculateMovesForPiece($piece, $this->game);
    }

    private function getCalculatorByPieceName(string $name): ?AbstractChessPieceMoveCalculator
    {
        $class = self::getCalculatorClassByPieceName($name);

        if ($class === null) {
            return null;
        }

        return app($class);
    }

    private function getCalculatorClassByPieceName(string $name): ?string
    {
        return match ($name) {
            ChessPieceDictionary::ROOK   => RookMoveCalculator::class,
            ChessPieceDictionary::BISHOP => BishopMoveCalculator::class,
            ChessPieceDictionary::QUEEN  => QueenMoveCalculator::class,
            ChessPieceDictionary::PAWN   => PawnMoveCalculator::class,
            ChessPieceDictionary::KNIGHT => KnightMoveCalculator::class,
            ChessPieceDictionary::KING   => KingMoveCalculator::class,
            default                      => null,
        };
    }
}
