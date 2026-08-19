<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Task API Endpoints
Route::apiResource('tasks', TaskController::class);

Route::get('/test-webhook', function() {
    $forwardedHost = request()->header('x-forwarded-host');
    $host = request()->getHost();

    try {
        $target = 'https://laravel-nodejs.onrender.com/webhook';
        $res = \Illuminate\Support\Facades\Http::withoutVerifying()->timeout(10)->post($target, [
            'event' => 'testPing',
            'data' => ['test' => true]
        ]);

        return response()->json([
            'status' => 'success',
            'target_url' => $target,
            'http_status' => $res->status(),
            'response_body' => $res->json(),
            'forwarded_host' => $forwardedHost,
            'host' => $host,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'forwarded_host' => $forwardedHost,
            'host' => $host,
        ], 500);
    }
});
