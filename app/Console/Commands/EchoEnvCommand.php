<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class EchoEnvCommand extends Command
{
    protected $signature = 'env:echo';
    protected $description = 'Output env to console';

    public function handle(): void
    {
        var_dump($_ENV);
        var_dump(config('database'));
    }
}
