<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;

class TaskController extends Controller
{

    
    public function index() {
        $task = Task::all();
        return response()->json($task);
    }

    public function store(Request $request) {

        $validatedData = $request->validate([
            'name' => 'unique:tasks',
        ]);

        $task = new Task;
        $task->name = $request->name;
        $task->description = $request->description;
        $task->due = $request->due;
        $task->status = $request->status;
        $task->save();

        return response()->json(['message'=>'Successfully added a task.']);
    }

    public function taskDone($id) {
        $task = Task::find($id);
        $task->status = 'Done';
        $task->save();

        return response()->json(['message' => 'Task successfully updated.']);
    }

    public function edit($id) {
        $task = Task::find($id);

        return response()->json($task);
    }

    public function update(Request $request, $id) {
        $validatedData = $request->validate([
            'name' => 'unique:tasks',
        ]);
        
        $task = Task::find($id);
        $task->name = $request->name;
        $task->description = $request->description;
        $task->due = $request->due;
        $task->save();

        return response()->json(['message' => 'Task Successfully updated.']);
    }

    public function delete($id) {
        $task = Task::find($id);
        $task->delete();
        return response()->json(['message' => 'Task Successfully deleted.']);
    }
}
