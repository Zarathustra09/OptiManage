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

        // Fetch the team task details
        $teamTask = TeamTask::findOrFail($request->team_task_id);

        // Check for overlapping tasks
        $overlappingTasks = TeamTask::where('id', '!=', $teamTask->id)
            ->whereHas('assignees', function($query) use ($request) {
                $query->where('user_id', $request->user_id);
            })
            ->whereNotIn('status', ['Finished', 'Cancel'])
            ->where(function($query) use ($teamTask) {
                $query->whereBetween('start_date', [$teamTask->start_date, $teamTask->end_date])
                    ->orWhereBetween('end_date', [$teamTask->start_date, $teamTask->end_date])
                    ->orWhere(function($query) use ($teamTask) {
                        $query->where('start_date', '<=', $teamTask->start_date)
                            ->where('end_date', '>=', $teamTask->end_date);
                    });
            })->exists();

        if ($overlappingTasks) {
            return response()->json(['error' => 'The task overlaps with an existing task for the user.'], 400);
        }

        $assignee = TeamAssignee::create($request->all());

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
