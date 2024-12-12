<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AvailabilityController extends Controller
{
    public function create($userId)
    {
        $user = User::findOrFail($userId);
        return view('admin.availability.create', compact('user'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $availabilities = $user->availabilities;
        return view('admin.availability.show', compact('user', 'availabilities'));
    }

    public function showSingle($id)
    {
        $availability = Availability::findOrFail($id);
        return response()->json($availability);
    }

    public function store(Request $request)
    {

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'day' => 'required|string',
            'available_from' => 'required|date_format:H:i',
            'available_to' => 'required|date_format:H:i|after:available_from',
            'status' => 'required|string',
        ]);

        $availability = Availability::create([
            'user_id' => $request->user_id,
            'day' => $request->day,
            'available_from' => $request->available_from,
            'available_to' => $request->available_to,
            'status' => $request->status,
        ]);

        return redirect()->route('availabilities.show', $availability->user_id)->with('success', 'Availability created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'day' => 'required|string',
            'available_from' => 'required|date_format:H:i',
            'available_to' => 'required|date_format:H:i|after:available_from',
            'status' => 'required|string',
        ]);

        $availability = Availability::findOrFail($id);
        $availability->update([
            'user_id' => $request->user_id,
            'day' => $request->day,
            'available_from' => $request->available_from,
            'available_to' => $request->available_to,
            'status' => $request->status,
        ]);

        return response()->json(['success' => true, 'message' => 'Availability updated successfully.']);
    }

    public function destroy($id)
    {
        $availability = Availability::findOrFail($id);
        $userId = $availability->user_id;
        $availability->delete();

        return response()->json(['success' => true, 'message' => 'Availability deleted successfully.']);
    }
}
