<?php

namespace App\Http\Controllers;

use App\Mail\TaskAssigned;
use App\Mail\TeamTaskAssigned;
use App\Models\TeamAssignee;
use App\Models\TeamTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        // Fetch the team task details
        $teamTask = TeamTask::findOrFail($request->team_task_id);

        // Fetch the user details
        $user = User::findOrFail($request->user_id);

        // Send the email
        Mail::to($user->email)->send(new TeamTaskAssigned($teamTask, $user));

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
