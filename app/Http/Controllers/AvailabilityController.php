<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\User;
use Carbon\Carbon;
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
            'shift_type' => 0, // Always set to day type
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
            'shift_type' => 0, // Always set to day type
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

    public function getAvailableUsers(Request $request)
    {
        Log::info('getAvailableUsers called', ['request' => $request->all()]);

        $startDate = Carbon::parse($request->query('start_date'));
        $endDate = Carbon::parse($request->query('end_date'));
        $areaId = $request->query('area_id');

        Log::info('Parsed dates', ['startDate' => $startDate, 'endDate' => $endDate]);

        // Check if start time is before 8 AM or end time is after 5 PM
        if ($startDate->hour < 8 || $endDate->hour >= 17) {
            Log::warning('Invalid shift time range', ['startDate' => $startDate, 'endDate' => $endDate]);
            return response()->json(['error' => 'Invalid shift time range. Start time must be after 8 AM and end time must be before 5 PM.'], 400);
        }

        $day = $startDate->format('l');
        Log::info('Day of the week', ['day' => $day]);

        $users = User::whereHas('availabilities', function ($query) use ($day) {
            $query->where('day', $day)
                ->where('shift_type', 0) // Day shift
                ->where('status', 'active');
        })
            ->when($areaId, function ($query, $areaId) {
                return $query->where('area_id', $areaId);
            })
            ->get();

        Log::info('Fetched users', ['users' => $users]);

        return response()->json($users);
    }
}
