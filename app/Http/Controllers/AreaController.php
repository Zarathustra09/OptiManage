<?php

// app/Http/Controllers/AreaController.php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::all();
        return view('admin.area.index', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $area = Area::create($request->all());
        return response()->json(['success' => 'Area created successfully.', 'area' => $area], 201);
    }

    public function show($id)
    {
        $area = Area::findOrFail($id);
        return response()->json($area);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $area = Area::findOrFail($id);
        $area->update($request->all());
        return response()->json(['success' => 'Area updated successfully.', 'area' => $area]);
    }

    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();
        return response()->json(['success' => 'Area deleted successfully.']);
    }

    public function allUser($id)
    {
        $area = Area::findOrFail($id);
        $users = $area->users;
        return response()->json($users);
    }

}
