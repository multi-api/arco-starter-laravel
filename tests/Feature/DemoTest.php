<?php

namespace Tests\Feature;

use App\Jobs\DemoJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use RuntimeException;
use Tests\TestCase;

class DemoTest extends TestCase
{
    private function sharedCache(): void
    {
        config([
            'cache.default' => 'dynamodb',
            'cache.stores.dynamodb' => ['driver' => 'array'],
        ]);
    }

    private function sharedStorage(): void
    {
        config(['filesystems.default' => 's3']);
        Storage::fake('s3');
    }

    public function test_the_demo_page_renders_with_no_resources(): void
    {
        config(['starter.database' => false]);

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('demo')
                ->where('features.queue', false)
                ->where('features.storage', false)
                ->where('features.cache', false)
                ->where('features.database', false)
                ->where('queue_result', null)
                ->where('files', []));
    }

    public function test_the_demo_page_lists_files_and_the_job_result_when_resources_exist(): void
    {
        $this->sharedStorage();
        $this->sharedCache();
        Storage::disk('s3')->put('demo/a.txt', 'a');
        Cache::put('demo:job_finished_at', '2026-01-01T00:00:00+00:00');

        $this->get('/')->assertInertia(fn (Assert $page) => $page
            ->where('files', ['demo/a.txt'])
            ->where('queue_result', '2026-01-01T00:00:00+00:00'));
    }

    public function test_posting_a_job_dispatches_it(): void
    {
        Queue::fake();

        $this->post('/demo/job')->assertNoContent();

        Queue::assertPushed(DemoJob::class);
    }

    public function test_the_job_logs_and_writes_to_the_cache_when_it_is_shared(): void
    {
        $this->sharedCache();
        Log::spy();

        (new DemoJob)->handle();

        Log::shouldHaveReceived('info')->with('Arco starter demo job finished')->once();
        $this->assertNotNull(Cache::get('demo:job_finished_at'));
        $this->getJson('/demo/job')->assertOk()->assertJson(['finished_at' => Cache::get('demo:job_finished_at')]);
    }

    public function test_the_job_falls_back_to_the_storage_disk(): void
    {
        $this->sharedStorage();

        (new DemoJob)->handle();

        Storage::disk('s3')->assertExists('demo/job-finished-at.txt');
        $this->getJson('/demo/job')->assertOk()->assertJson([
            'finished_at' => Storage::disk('s3')->get('demo/job-finished-at.txt'),
        ]);
    }

    public function test_the_job_only_logs_when_there_is_no_cache_or_storage(): void
    {
        Log::spy();

        (new DemoJob)->handle();

        Log::shouldHaveReceived('info')->with('Arco starter demo job finished')->once();
        $this->getJson('/demo/job')->assertOk()->assertJson(['finished_at' => null]);
    }

    public function test_a_file_is_stored_on_the_default_disk(): void
    {
        $this->sharedStorage();

        $this->post('/demo/files', ['file' => UploadedFile::fake()->create('hello.txt', 10)])
            ->assertRedirect();

        Storage::disk('s3')->assertExists('demo/hello.txt');
    }

    public function test_a_file_over_one_megabyte_is_rejected(): void
    {
        $this->sharedStorage();

        $this->post('/demo/files', ['file' => UploadedFile::fake()->create('big.bin', 2048)])
            ->assertSessionHasErrors('file');
    }

    public function test_uploading_returns_409_without_shared_storage(): void
    {
        Storage::fake();

        $this->postJson('/demo/files', ['file' => UploadedFile::fake()->create('hello.txt', 10)])
            ->assertStatus(409);

        Storage::disk()->assertMissing('demo/hello.txt');
    }

    public function test_the_counter_increments(): void
    {
        $this->sharedCache();

        $this->postJson('/demo/counter')->assertOk()->assertJson(['count' => 1]);
        $this->postJson('/demo/counter')->assertOk()->assertJson(['count' => 2]);
    }

    public function test_the_counter_returns_409_without_a_shared_cache(): void
    {
        $this->postJson('/demo/counter')->assertStatus(409);
    }

    public function test_the_log_endpoint_writes_a_line(): void
    {
        Log::spy();

        $this->post('/demo/log')->assertNoContent();

        Log::shouldHaveReceived('info')->with('Arco starter demo log line')->once();
    }

    public function test_the_error_endpoint_throws_and_is_reported(): void
    {
        Exceptions::fake();

        $this->post('/demo/error')->assertStatus(500);

        Exceptions::assertReported(fn (RuntimeException $e) => $e->getMessage() === 'Arco starter test exception');
    }
}
