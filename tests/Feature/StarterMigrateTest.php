<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StarterMigrateTest extends TestCase
{
    public function test_it_skips_without_a_database()
    {
        putenv('DB_CONNECTION');
        config(['starter.database' => false]);

        $this->artisan('starter:migrate')
            ->expectsOutput('No database yet, skipping migrations.')
            ->assertExitCode(0);
    }

    public function test_it_migrates_with_a_database()
    {
        config(['starter.database' => true]);

        $this->assertSame(0, Artisan::call('starter:migrate'));
        $this->assertTrue(Schema::hasTable('migrations'));
    }
}
