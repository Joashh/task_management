<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\task;

class TaskController extends AuthController
{
    public function store(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string|in:pending,completed',
            'priority' => 'required|string|in:low,medium,high',
            'user_ids' => 'required|array',              // array of users
            'user_ids.*' => 'exists:users,id',          // each user exists
        ]);

        // Get the current max order
        $maxOrder = Task::max('order') ?? 0;

        $tasks = [];
        foreach ($validated['user_ids'] as $userId) {
            $maxOrder++;  // increment order for each new task
            $tasks[] = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'status' => $validated['status'],
                'priority' => $validated['priority'],
                'order' => $maxOrder,
                'user_id' => $userId,
            ]);
        }

        return response()->json([
            'message' => 'Task(s) created successfully',
            'tasks' => $tasks
        ], 201);
    }

    public function alltask()
    {
        $tasklist = task::all();
        return response()->json($tasklist);
    }

    public function myTasks()
    {
        return Task::where('user_id', auth()->id())
               ->orderBy('order', 'asc')
               ->get();
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->tasks as $t) {
            Task::where('id', $t['id'])->update(['order' => $t['order']]);
        }

        return response()->json(['message' => 'Order updated']);
    }

    public function update(Request $request, Task $task)
{
    $task->update($request->only('status'));
    return response()->json($task);
}

}
