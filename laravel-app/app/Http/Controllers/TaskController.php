<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of all tasks.
     */
    public function index()
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return response()->json($tasks);
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_completed' => false,
        ]);

        $this->notifyNodeServer('taskCreated', $task);

        return response()->json($task, 201);
    }

    /**
     * Display the specified task.
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        return response()->json($task);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'sometimes|boolean',
        ]);

        $task->update($validated);

        $this->notifyNodeServer('taskUpdated', $task);

        return response()->json($task);
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $deletedTaskData = $task->toArray();

        $task->delete();

        $this->notifyNodeServer('taskDeleted', $deletedTaskData);

        return response()->json([
            'message' => 'Task deleted successfully',
            'id' => (int) $id
        ]);
    }

    /**
     * Helper to dispatch webhook POST notification to Node.js server.
     */
    protected function notifyNodeServer(string $event, $data): void
    {
        try {
            $nodeUrl = env('NODE_SERVER_URL');
            $forwardedHost = request()->header('x-forwarded-host') ?? request()->getHost();

            // If request comes through Render (X-Forwarded-Host contains onrender.com or non-local host)
            if (str_contains($forwardedHost, 'onrender.com') || (!str_contains($forwardedHost, 'localhost') && !str_contains($forwardedHost, '127.0.0.1'))) {
                $nodeUrl = 'https://laravel-nodejs.onrender.com/webhook';
            } elseif (empty($nodeUrl)) {
                $nodeUrl = 'http://127.0.0.1:3000/webhook';
            }

            $response = Http::withoutVerifying()->timeout(10)->post($nodeUrl, [
                'event' => $event,
                'data'  => $data,
                'timestamp' => now()->toIso8601String()
            ]);

            if ($response->failed()) {
                Log::warning("Node.js Webhook returned non-200 status ({$response->status()}): " . $response->body());
            }
        } catch (\Throwable $e) {
            Log::warning("Node.js Webhook notification failed to {$nodeUrl}: " . $e->getMessage());
        }
    }
}
