<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::all();
        $areas = Area::all(); // Fetch all areas
        return view('admin.team.index', compact('teams', 'areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        $team = Team::create($request->all());
        return response()->json(['success' => 'Team created successfully.', 'team' => $team], 201);
    }

    public function show($id)
    {
        $team = Team::with('assignees.user', 'area')->findOrFail($id);
        $users = User::where('area_id', $team->area_id)->get(); // Filter users by team's area
        return view('admin.team.show', compact('team', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'area_id' => 'nullable|exists:areas,id',
        ]);

        $team = Team::findOrFail($id);
        $team->update($request->all());
        return response()->json(['success' => 'Team updated successfully.', 'team' => $team]);
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();
        return response()->json(['success' => 'Team deleted successfully.']);
    }
}
