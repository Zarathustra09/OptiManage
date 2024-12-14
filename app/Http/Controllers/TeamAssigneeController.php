<?php

namespace App\Http\Controllers;

use App\Models\TeamAssignee;
use Illuminate\Http\Request;

class TeamAssigneeController extends Controller
{
    public function index()
    {
        $assignees = TeamAssignee::with('user', 'teamTask')->get();
        return response()->json($assignees);
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_task_id' => 'required|exists:team_tasks,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $assignee = TeamAssignee::create($request->all());
        return response()->json($assignee, 201);
    }

    public function show($id)
    {
        $assignee = TeamAssignee::with('user', 'teamTask')->findOrFail($id);
        return response()->json($assignee);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'team_task_id' => 'required|exists:team_tasks,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $assignee = TeamAssignee::findOrFail($id);
        $assignee->update($request->all());
        return response()->json($assignee);
    }

    public function destroy($id)
    {
        $assignee = TeamAssignee::findOrFail($id);
        $assignee->delete();
        return response()->json(['success' => 'Team Assignee deleted successfully.']);
    }
}
