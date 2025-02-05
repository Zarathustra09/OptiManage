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
        'shift_type' => 'required|integer|in:0,1',
    ]);

    $availability = Availability::create([
        'user_id' => $request->user_id,
        'day' => $request->day,
        'available_from' => $request->available_from,
        'available_to' => $request->available_to,
        'status' => $request->status,
        'shift_type' => $request->shift_type,
    ]);

    return redirect()->route('availabilities.show', $availability->user_id)->with('success', 'Availability created successfully.');
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'day' => 'required|string',
            'available_from' => 'required|date_format:H:i',
            'available_to' => 'required|date_format:H:i|',
            'status' => 'required|string',
            'shift_type' => 'required|integer|in:0,1',
        ]);

        $availability = Availability::findOrFail($id);
        $availability->update([
            'user_id' => $request->user_id,
            'day' => $request->day,
            'available_from' => $request->available_from,
            'available_to' => $request->available_to,
            'status' => $request->status,
            'shift_type' => $request->shift_type,
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
        $startDate = Carbon::parse($request->query('start_date'));
        $endDate = Carbon::parse($request->query('end_date'));

        // Determine shift type based on start and end times
        $shiftType = null;
        $startHour = $startDate->hour;
        $endHour = $endDate->hour;

        if ($startHour >= 8 && $startHour < 17 && $endHour >= 8 && $endHour <= 17) {
            $shiftType = 0; // Day shift
        } elseif (($startHour >= 20 || $startHour < 5) && ($endHour >= 20 || $endHour < 5)) {
            $shiftType = 1; // Night shift
        } else {
            return response()->json(['error' => 'Invalid shift time range'], 400);
        }

        Log::info('Shift type: ' . $shiftType);

        $day = $startDate->format('l');

        $users = User::whereHas('availabilities', function ($query) use ($day, $shiftType) {
            $query->where('day', $day)
                ->where('shift_type', $shiftType)
                ->where('status', 'active');
        })->get();

        return response()->json($users);
    }
}
