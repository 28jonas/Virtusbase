<?php

namespace App\Http\Controllers\API;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;

class TodoController extends BaseApiController
{
    public function index(Request $request)
    {
        Log::info('Todo index request:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'show_completed' => 'nullable|boolean',
            'show_pending' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $userId = request()->user['id'];

        // Basis query voor todos van de gebruiker
        $query = Todo::where('profile_id', $userId);

        // Filter opties
        if ($request->has('show_completed') && $request->show_completed === true) {
            $query->where('completed', true)
                  ->where('completed_at', '>', now()->subDay());
        }

        if ($request->has('show_pending') && $request->show_pending === true) {
            $query->where('completed', false);
        }

        // Standaard: toon alle recente todos (zoals in Livewire component)
        if (!$request->has('show_completed') && !$request->has('show_pending')) {
            $query->where(function($q) {
                $q->where('completed', false)
                  ->orWhere('completed_at', '>', now()->subDay());
            });
        }

        // Sortering zoals in Livewire component
        $todos = $query->orderByRaw('completed ASC, date ASC')
                      ->get();

        // Optioneel: aparte arrays voor completed en pending zoals in Livewire
        $completedTodos = Todo::where('profile_id', $userId)
            ->where('completed', true)
            ->where('completed_at', '>', now()->subDay())
            ->orderBy('completed_at', 'desc')
            ->get();

        $pendingTodos = Todo::where('profile_id', $userId)
            ->where('completed', false)
            ->orderBy('date', 'asc')
            ->get();

        $response = [
            'todos' => $todos,
            'completed_todos' => $completedTodos,
            'pending_todos' => $pendingTodos,
            'stats' => [
                'total' => $todos->count(),
                'completed' => $completedTodos->count(),
                'pending' => $pendingTodos->count(),
            ]
        ];

        return $this->sendResponse($response, 'Todos retrieved successfully.');
    }

    public function store(Request $request)
    {
        Log::info('Store todo request data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $userId = request()->user['id'];

        $todo = Todo::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'completed' => false,
            'profile_id' => $userId,
        ]);

        // Verwijder oude completed todos (ouder dan 24 uur)
        $this->cleanupOldCompletedTodos($userId);

        return $this->sendResponse($todo, 'Todo created successfully.', 201);
    }

    public function show(Todo $todo)
    {
        // Authorisatie: controleer of todo van de gebruiker is
        $userId = request()->user['id'];
        if ($todo->profilz_id != $userId) {
            return $this->sendError('Unauthorized access to todo.', [], 403);
        }

        return $this->sendResponse($todo, 'Todo retrieved successfully.');
    }

    public function update(Request $request, Todo $todo)
    {
        Log::info('Update todo request data:', $request->all());
        
        // Authorisatie: controleer of todo van de gebruiker is
        $userId = request()->user['id'];
        if ($todo->profile_id != $userId) {
            return $this->sendError('Unauthorized access to todo.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $todo->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return $this->sendResponse($todo, 'Todo updated successfully.');
    }

    public function destroy(Todo $todo)
    {
        // Authorisatie: controleer of todo van de gebruiker is
        $userId = request()->user['id'];
        if ($todo->profile_id != $userId) {
            return $this->sendError('Unauthorized access to todo.', [], 403);
        }

        $todo->delete();

        return $this->sendResponse(null, 'Todo deleted successfully.');
    }

    public function toggleComplete(Todo $todo)
    {
        // Authorisatie: controleer of todo van de gebruiker is
        $userId = request()->user['id'];
        if ($todo->profile_id != $userId) {
            return $this->sendError('Unauthorized access to todo.', [], 403);
        }

        $todo->update([
            'completed' => !$todo->completed,
            'completed_at' => $todo->completed ? null : now(),
        ]);

        // Verwijder oude completed todos (ouder dan 24 uur)
        $this->cleanupOldCompletedTodos($userId);

        return $this->sendResponse($todo, 'Todo completion status updated successfully.');
    }

    public function bulkDeleteCompleted()
    {
        $userId = request()->user['id'];
        
        $deletedCount = Todo::where('profile_id', $userId)
            ->where('completed', true)
            ->delete();

        return $this->sendResponse(['deleted_count' => $deletedCount], 'Completed todos deleted successfully.');
    }

    public function cleanupOldCompletedTodos($userId = null)
    {
        if (!$userId) {
            $userId = request()->user['id'];
        }

        $deletedCount = Todo::where('profile_id', $userId)
            ->where('completed', true)
            ->where('completed_at', '<', now()->subDay())
            ->delete();

        Log::info("Cleaned up {$deletedCount} old completed todos for user {$userId}");

        return $deletedCount;
    }

    public function getStats()
    {
        Log::info('Get todo stats request received');
        $userId = request()->user['id'];

        $total = Todo::where('profile_id', $userId)->count();
        $completed = Todo::where('profile_id', $userId)
            ->where('completed', true)
            ->where('completed_at', '>', now()->subDay())
            ->count();
        $pending = Todo::where('profile_id', $userId)
            ->where('completed', false)
            ->count();

        $stats = [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
        ];

        return $this->sendResponse($stats, 'Todo stats retrieved successfully.');
    }
}