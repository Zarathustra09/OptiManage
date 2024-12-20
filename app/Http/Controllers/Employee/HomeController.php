<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $employeeCount = $this->getEmployeeCount();
        $finishedTaskCount = $this->getFinishedTaskCount();
        $onProgressTaskCount = $this->getOnProgressTaskCount();
        $toBeApprovedTaskCount = $this->getToBeApprovedTaskCount();
        $latestTasks = Task::where('user_id', Auth::id())->latest()->take(8)->get();
        return view('employee.home', compact('employeeCount', 'finishedTaskCount', 'onProgressTaskCount', 'toBeApprovedTaskCount', 'latestTasks'));
    }

    private function getEmployeeCount()
    {
        return Auth::user()->where('role_id', 1)->count();
    }

    private function getFinishedTaskCount()
    {
        return Task::where('user_id', Auth::id())->where('status', 'Finished')->count();
    }

    private function getOnProgressTaskCount()
    {
        return Task::where('user_id', Auth::id())->where('status', 'On Progress')->count();
    }

    private function getToBeApprovedTaskCount()
    {
        return Task::where('user_id', Auth::id())->where('status', 'To be Approved')->count();
    }
}
