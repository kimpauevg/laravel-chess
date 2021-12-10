<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

abstract class AbstractFactory extends Factory
{
    public function id(int $id): static
    {
        return $this->state(['id' => $id]);
    }
}
