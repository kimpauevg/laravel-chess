<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

class ChessGamePage extends Page
{
    public function __construct(
        private int $id,
    ) {
    }

    public function url(): string
    {
        return '/chess-games/' . $this->id;
    }
}
