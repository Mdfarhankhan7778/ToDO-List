<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        return Task::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:tasks'
        ]);

        $task = Task::create([
            'title' => $request->title,
            'completed' => false
        ]);

        return $task;
    }

    public function update(Request $request, Task $task)
    {
        $task->completed = $request->completed;
        $task->save();

        return $task;
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(['success' => true]);
    }
}

