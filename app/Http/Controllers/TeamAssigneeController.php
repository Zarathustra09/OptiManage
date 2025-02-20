<?php

// app/Http/Controllers/TeamAssigneeController.php

namespace App\Http\Controllers;

use App\Models\TeamAssignee;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamTaskAssigned;

class TeamAssigneeController extends Controller
{
    public function index()
    {
        $assignees = TeamAssignee::with('user', 'team')->get();
        return response()->json($assignees);
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $assignee = TeamAssignee::create($request->all());

        // Fetch the user details
        $user = User::findOrFail($request->user_id);

//        // Send the email
//        Mail::to($user->email)->send(new TeamTaskAssigned($assignee->team, $user));

        return response()->json($assignee, 201);
    }

    public function show($id)
    {
        $assignee = TeamAssignee::with('user', 'team')->findOrFail($id);
        return response()->json($assignee);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
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
