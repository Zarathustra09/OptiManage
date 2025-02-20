<?php

// app/Http/Controllers/EmployeeController.php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\User;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $users = User::where('role_id', 0)->with('area')->get();
        return view('admin.employee.index', compact('users'));
    }

    public function create()
    {
        $areas = Area::all();
        return view('admin.employee.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15|unique:users',
            'employee_id' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'area_id' => 'required|exists:areas,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role_id' => 0,
            'employee_id' => $request->employee_id,
            'area_id' => $request->area_id,
        ]);

        $shiftTimings = Config::get('shifts.day');
        $shiftType = $shiftTimings['shift_type'];
        unset($shiftTimings['shift_type']);

        foreach ($shiftTimings as $day => $timing) {
            Availability::create([
                'user_id' => $user->id,
                'day' => $day,
                'available_from' => $timing['from'],
                'available_to' => $timing['to'],
                'status' => 'active',
                'shift_type' => $shiftType,
            ]);
        }

        return response()->json(['success' => 'Employee created successfully']);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $areas = Area::all();
        return view('admin.employee.show', compact('user', 'areas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:15|unique:users,phone_number,' . $id,
            'employee_id' => 'required|string|max:255|unique:users,employee_id,' . $id,
            'area_id' => 'required|exists:areas,id',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->all());

        return response()->json(['success' => 'Employee updated successfully']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['success' => 'Employee deleted successfully']);
    }
}
