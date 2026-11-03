<?php

namespace Tests\Feature;

use Tests\TestCase;

class ArcoConfigTest extends TestCase
{
    public function test_arco_php_ships_the_two_lines_arco_cloud_rewrites()
    {
        $src = file_get_contents(base_path('arco.php'));

        $this->assertStringContainsString("App::make('arco-starter')", $src);
        $this->assertStringContainsString("->region('us-east-1')", $src);
        $this->assertSame(1, substr_count($src, "'arco-starter'"));
    }

    public function test_defaults_need_no_database()
    {
        // phpunit.xml overrides these env vars, so assert the fallback literals the config files ship.
        $this->assertStringContainsString("env('SESSION_DRIVER', 'cookie')", file_get_contents(base_path('config/session.php')));
        $this->assertStringContainsString("env('CACHE_STORE', 'array')", file_get_contents(base_path('config/cache.php')));
        $this->assertStringContainsString("env('QUEUE_CONNECTION', 'sync')", file_get_contents(base_path('config/queue.php')));
    }

    public function test_the_dsql_connection_reads_the_schema_from_dsql_search_path()
    {
        $src = file_get_contents(base_path('config/database.php'));

        $this->assertStringContainsString("'driver' => 'dsql'", $src);
        $this->assertStringContainsString("'search_path' => env('DSQL_SEARCH_PATH', 'public')", $src);
    }
}
