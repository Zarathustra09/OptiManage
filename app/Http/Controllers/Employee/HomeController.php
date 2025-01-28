<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TeamTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        $employeeCount = $this->getEmployeeCount();
        $finishedTaskCount = $this->getFinishedTaskCount();
        $onProgressTaskCount = $this->getOnProgressTaskCount();
        $toBeApprovedTaskCount = $this->getToBeApprovedTaskCount();
        $latestTasks = $this->getLatestTasks();

        return view('employee.home', compact('employeeCount', 'finishedTaskCount', 'onProgressTaskCount', 'toBeApprovedTaskCount', 'latestTasks'));
    }

    private function getEmployeeCount()
    {
        return Auth::user()->where('role_id', 1)->count();
    }

    private function getFinishedTaskCount()
    {
        $finishedTaskCount = Task::where('status', 'Finished')->count();
        $finishedTeamTaskCount = TeamTask::whereHas('assignees', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('status', 'Finished')->count();
        return $finishedTaskCount + $finishedTeamTaskCount;
    }

    private function getOnProgressTaskCount()
    {
        $onProgressTaskCount = Task::where('status', 'On Progress')->count();
        $onProgressTeamTaskCount = TeamTask::whereHas('assignees', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('status', 'On Progress')->count();
        return $onProgressTaskCount + $onProgressTeamTaskCount;
    }

    private function getToBeApprovedTaskCount()
    {
        $toBeApprovedTaskCount = Task::where('status', 'To be Approved')->count();
        $toBeApprovedTeamTaskCount = TeamTask::whereHas('assignees', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('status', 'To be Approved')->count();
        return $toBeApprovedTaskCount + $toBeApprovedTeamTaskCount;
    }

    private function getLatestTasks()
    {
        $latestTasks = Task::where('user_id', Auth::id())->latest()->take(4)->get();
        Log::info('Latest Tasks: '.$latestTasks);
        $latestTeamTasks = TeamTask::whereHas('assignees', function ($query) {
            $query->where('user_id', Auth::id());
        })->latest()->take(4)->get();
        Log::info('Latest Team Tasks: '. $latestTeamTasks);
        $mergedTasks = $latestTasks->merge($latestTeamTasks)->sortByDesc('created_at')->take(4);
        Log::info('Merged Tasks: '. $mergedTasks);
        return $mergedTasks;
    }
}
