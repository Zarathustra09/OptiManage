<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\TeamTask;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeTeamTaskController extends Controller
{
    public function index()
    {
        $tasks = TeamTask::whereHas('assignees', function($query) {
            $query->where('user_id', Auth::id());
        })->get();
        return view('employee.teamTask.index', compact('tasks'));
    }

    public function show($id)
    {
        $teamTask = TeamTask::with(['assignees.user', 'inventories', 'images'])->findOrFail($id);
        $employees = User::where('role_id', 0)->get(['id', 'name']);
        return view('employee.teamTask.show', compact('teamTask', 'employees'));
    }

//    public function update(Request $request, $id)
//    {
//        $request->validate([
//            'proof_of_work' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
//        ]);
//
//        $task = TeamTask::findOrFail($id);
//
//        // Delete the old proof of work if it exists
//        if ($task->proof_of_work) {
//            Storage::delete('public/' . $task->proof_of_work);
//        }
//
//        // Store the new proof of work
//        $filePath = $request->file('proof_of_work')->store('proof_of_work', 'public');
//
//        // Update the task with the new proof of work path
//        $task->proof_of_work = $filePath;
//        $task->save();
//
//        return response()->json(['success' => 'Proof of work updated successfully.']);
//    }
}
