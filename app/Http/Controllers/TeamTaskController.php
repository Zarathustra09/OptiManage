<?php

namespace App\Http\Controllers;

use App\Models\TeamTask;
use App\Models\User;
use Illuminate\Http\Request;

class TeamTaskController extends Controller
{
    public function index()
    {
        $tasks = TeamTask::with('assignees')->get();
        return view('admin.teamTask.index', compact('tasks'));
    }

    public function create()
    {
        return view('admin.teamTask.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'proof_of_work' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $prefix = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5));
        $suffix = substr(str_shuffle('0123456789'), 0, 5);
        $ticket_id = $prefix . '-' . $suffix;

        $data = $request->all();
        if ($request->hasFile('proof_of_work')) {
            $file = $request->file('proof_of_work');
            $path = $file->store('proof_of_work', 'public');
            $data['proof_of_work'] = $path;
        }

        TeamTask::create(array_merge($data, ['ticket_id' => $ticket_id]));

        return redirect()->route('admin.teamTask.index')->with('success', 'Team Task created successfully.');
    }

    public function show($id)
    {
        $task = TeamTask::with('assignees.user')->findOrFail($id);
        $employees = User::where('role_id', 0)->get(['id', 'name']);
        return view('admin.teamTask.show', compact('task', 'employees'));
    }

    public function showSingle($id)
    {
        $task = TeamTask::findOrFail($id);
        return response()->json($task);
    }


    public function edit($id)
    {
        $task = TeamTask::findOrFail($id);
        return view('admin.teamTask.edit', compact('task'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'proof_of_work' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $task = TeamTask::findOrFail($id);
        $data = $request->all();
        if ($request->hasFile('proof_of_work')) {
            $file = $request->file('proof_of_work');
            $path = $file->store('proof_of_work', 'public');
            $data['proof_of_work'] = $path;
        }

        $task->update($data);

        return redirect()->route('admin.teamTask.index')->with('success', 'Team Task updated successfully.');
    }

    public function destroy($id)
    {
        $task = TeamTask::findOrFail($id);
        $task->delete();

        return response()->json(['success' => 'Team Task has been deleted successfully.']);
    }
}
