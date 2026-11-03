<?php

namespace App\Http\Controllers;

use App\Jobs\DemoJob;
use App\Support\Features;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use RuntimeException;

class DemoController extends Controller
{
    public function show(): InertiaResponse
    {
        $features = Features::current();

        return Inertia::render('demo', [
            'features' => $features,
            'queue_result' => DemoJob::finishedAt(),
            'files' => $features['storage'] ? Storage::files('demo') : [],
        ]);
    }

    public function dispatchJob(): Response
    {
        DemoJob::dispatch();

        return response()->noContent();
    }

    public function jobResult(): JsonResponse
    {
        return response()->json(['finished_at' => DemoJob::finishedAt()]);
    }

    public function upload(Request $request): RedirectResponse|JsonResponse
    {
        if (! Features::current()['storage']) {
            return response()->json(['message' => 'No shared storage is configured.'], 409);
        }

        $request->validate(['file' => ['required', 'file', 'max:1024']]);

        $request->file('file')->storeAs('demo', $request->file('file')->getClientOriginalName());

        return back();
    }

    public function counter(): JsonResponse
    {
        if (! Features::current()['cache']) {
            return response()->json(['message' => 'No shared cache is configured.'], 409);
        }

        // DynamoDB's increment fails on a missing key, so create it first.
        Cache::add('demo:counter', 0);

        return response()->json(['count' => (int) Cache::increment('demo:counter')]);
    }

    public function log(): Response
    {
        Log::info('Arco starter demo log line');

        return response()->noContent();
    }

    public function error(): never
    {
        throw new RuntimeException('Arco starter test exception');
    }
}
