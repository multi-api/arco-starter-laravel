<?php

namespace Tests\Feature;

use App\Support\Features;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FeaturesTest extends TestCase
{
    use RefreshDatabase;

    public static function flags(): array
    {
        return [
            'queue on' => ['queue.default', 'sqs', 'queue', true],
            'queue off' => ['queue.default', 'sync', 'queue', false],
            'cache on' => ['cache.default', 'dynamodb', 'cache', true],
            'cache off' => ['cache.default', 'array', 'cache', false],
            'storage on' => ['filesystems.default', 's3', 'storage', true],
            'storage off (local)' => ['filesystems.default', 'local', 'storage', false],
            'storage off (public)' => ['filesystems.default', 'public', 'storage', false],
            'database on' => ['starter.database', true, 'database', true],
            'database off' => ['starter.database', false, 'database', false],
        ];
    }

    #[DataProvider('flags')]
    public function test_each_flag_follows_its_config_value(string $key, mixed $value, string $flag, bool $expected)
    {
        config([$key => $value]);

        $this->assertSame($expected, Features::current()[$flag]);
    }

    public function test_it_reports_the_environment()
    {
        $env = Features::current()['environment'];

        $this->assertSame('testing', $env['app_env']);
        $this->assertSame(PHP_VERSION, $env['php']);
        $this->assertArrayHasKey('arco_env', $env);
        $this->assertArrayHasKey('region', $env);
    }

    public function test_login_works_with_a_database()
    {
        $this->assertTrue(config('starter.database'));

        $this->get('/login')->assertOk();
    }

    public function test_login_routes_404_without_a_database()
    {
        // Routes register at boot, so reboot the app with DB_CONNECTION unset.
        // The string 'null' is how env() spells null; a plain unset would let .env win.
        $this->bootWithoutDatabase();

        $this->assertFalse(config('starter.database'));
        $this->get('/login')->assertNotFound();
        $this->get('/register')->assertNotFound();
        $this->get('/settings/profile')->assertNotFound();
        $this->get('/dashboard')->assertNotFound();
        $this->get('/')->assertOk();
    }

    private function bootWithoutDatabase(): void
    {
        $previous = [$_ENV['DB_CONNECTION'] ?? null, $_SERVER['DB_CONNECTION'] ?? null, getenv('DB_CONNECTION')];

        putenv('DB_CONNECTION=null');
        $_ENV['DB_CONNECTION'] = $_SERVER['DB_CONNECTION'] = 'null';

        $this->beforeApplicationDestroyed(function () use ($previous) {
            [$env, $server, $process] = $previous;
            $env === null ? $this->unsetGlobal($_ENV) : $_ENV['DB_CONNECTION'] = $env;
            $server === null ? $this->unsetGlobal($_SERVER) : $_SERVER['DB_CONNECTION'] = $server;
            putenv($process === false ? 'DB_CONNECTION' : "DB_CONNECTION=$process");
        });

        $this->refreshApplication();
    }

    private function unsetGlobal(array &$globals): void
    {
        unset($globals['DB_CONNECTION']);
    }
}
