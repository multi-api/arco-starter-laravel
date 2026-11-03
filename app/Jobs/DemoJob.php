<?php

namespace App\Jobs;

use App\Support\Features;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DemoJob implements ShouldQueue
{
    use Queueable;

    public const CACHE_KEY = 'demo:job_finished_at';

    public const STORAGE_PATH = 'demo/job-finished-at.txt';

    /**
     * Log a line and, when a place shared with the web function exists,
     * record when the job finished so the demo page can show it.
     */
    public function handle(): void
    {
        Log::info('Arco starter demo job finished');

        $features = Features::current();
        $finishedAt = now()->toIso8601String();

        if ($features['cache']) {
            Cache::put(self::CACHE_KEY, $finishedAt, now()->addHour());
        } elseif ($features['storage']) {
            Storage::put(self::STORAGE_PATH, $finishedAt);
        }
    }

    /**
     * When the job last finished, or null if nothing shared records it.
     */
    public static function finishedAt(): ?string
    {
        $features = Features::current();

        if ($features['cache']) {
            return Cache::get(self::CACHE_KEY);
        }

        if ($features['storage'] && Storage::exists(self::STORAGE_PATH)) {
            return Storage::get(self::STORAGE_PATH);
        }

        return null;
    }
}
