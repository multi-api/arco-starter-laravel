<?php

namespace App\Support;

class Features
{
    /**
     * Which Arco resources this deployment has, read from the config Arco sets.
     *
     * @return array{queue: bool, storage: bool, cache: bool, database: bool, environment: array{app_env: string, arco_env: ?string, php: string, region: ?string}}
     */
    public static function current(): array
    {
        return [
            'queue' => config('queue.default') === 'sqs',
            'storage' => ! in_array(config('filesystems.default'), ['local', 'public'], true),
            'cache' => config('cache.default') === 'dynamodb',
            'database' => (bool) config('starter.database'),
            'environment' => [
                'app_env' => (string) config('app.env'),
                'arco_env' => env('ARCO_ENV'),
                'php' => PHP_VERSION,
                'region' => env('AWS_REGION'),
            ],
        ];
    }
}
