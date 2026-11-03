<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StarterMigrate extends Command
{
    protected $signature = 'starter:migrate';

    protected $description = 'Run migrations once a database exists; skip cleanly before that';

    public function handle(): int
    {
        if (! config('starter.database')) {
            $this->info('No database yet, skipping migrations.');

            return self::SUCCESS;
        }

        return $this->call('migrate', ['--force' => true]);
    }
}
