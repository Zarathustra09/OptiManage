<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class EmployeeLogController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $logs = Activity::where('causer_id', $userId)->get()->map(function ($log) {
            if (class_exists($log->subject_type)) {
                $log->subject = $log->subject_type::find($log->subject_id);
            }
            if (class_exists($log->causer_type)) {
                $log->causer = $log->causer_type::find($log->causer_id);
            }
            return $log;
        });
        return view('employee.log.index', compact('logs'));
    }
}
