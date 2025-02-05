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
        $shiftType = $request->query('shift_type');

        // Get shift configuration
        $shifts = config('shifts');
        $shift = $shiftType === '1' ? $shifts['night'] : $shifts['day'];
        $day = $startDate->format('l');

        // Get configured shift times
        $shiftStart = Carbon::parse($shift[$day]['from']);
        $shiftEnd = Carbon::parse($shift[$day]['to']);

        // Check if time is within shift range
        $requestStart = Carbon::parse($startDate->format('H:i'));
        $requestEnd = Carbon::parse($endDate->format('H:i'));

        if ($shiftType === '0') {
            // Day shift validation (8:00-17:00)
            if ($requestStart->lt($shiftStart) || $requestEnd->gt($shiftEnd)) {
                return response()->json(['error' => 'Time must be between 08:00-17:00 for day shift'], 400);
            }
        } else {
            // Night shift validation (20:00-05:00)
            if ($requestStart->lt($shiftStart) && $requestStart->gt($shiftEnd)) {
                return response()->json(['error' => 'Time must be between 20:00-05:00 for night shift'], 400);
            }
        }

        $users = User::whereHas('availabilities', function ($query) use ($day, $shiftType) {
            $query->where('day', $day)
                ->where('shift_type', $shiftType)
                ->where('status', 'active');
        })

//            ->with(['availabilities' => function ($query) use ($day) {
//            $query->where('day', $day);
        ->get();

        return response()->json($users);
    }
}
