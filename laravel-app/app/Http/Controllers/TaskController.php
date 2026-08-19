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
            $nodeUrl = env('NODE_SERVER_URL', 'https://laravel-nodejs.onrender.com/webhook');
            
            // Auto-fallback: If request comes from a live production domain (not localhost/127.0.0.1)
            // but nodeUrl points to local 127.0.0.1/localhost, automatically route to production Node server.
            $host = request()->getHost();
            if ($host !== 'localhost' && $host !== '127.0.0.1' && (str_contains($nodeUrl, '127.0.0.1') || str_contains($nodeUrl, 'localhost') || empty($nodeUrl))) {
                $nodeUrl = 'https://laravel-nodejs.onrender.com/webhook';
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
