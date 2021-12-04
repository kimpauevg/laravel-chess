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

        $calculator = $this->getCalculatorByName($piece->name);

        if ($calculator === null) {
            return new CoordinateCollection();
        }

        return $calculator->calculateMovesForPiece($piece);
    }

    private function getCalculatorByName(string $name): ?AbstractChessPieceMoveCalculator
    {
        switch ($name) {
            case ChessPieceDictionary::ROOK:
                return (new RookMoveCalculator($this->game));

            case ChessPieceDictionary::BISHOP:
                return (new BishopMoveCalculator($this->game));

            case ChessPieceDictionary::QUEEN:
                return (new QueenMoveCalculator($this->game));

            case ChessPieceDictionary::PAWN:
                return (new PawnMoveCalculator($this->game));

            case ChessPieceDictionary::KNIGHT:
                return (new KnightMoveCalculator($this->game));

            case ChessPieceDictionary::KING:
                return (new KingMoveCalculator($this->game));
        }

        return null;
    }
}
