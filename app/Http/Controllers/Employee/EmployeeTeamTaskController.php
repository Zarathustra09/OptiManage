<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\TeamTask;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
        $task = TeamTask::with(['assignees.user', 'inventories'])->findOrFail($id);
        $employees = User::where('role_id', 0)->get(['id', 'name']);
        return view('employee.teamTask.show', compact('task', 'employees'));
    }
}
