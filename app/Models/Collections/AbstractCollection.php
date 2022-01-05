<?php

declare(strict_types=1);

namespace App\Models\Collections;

use Illuminate\Database\Eloquent\Collection;

abstract class AbstractCollection extends Collection
{
    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function doesNotExist(): bool
    {
        return !$this->exists();
    }
}
