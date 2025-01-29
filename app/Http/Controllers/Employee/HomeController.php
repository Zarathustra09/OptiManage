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
        $finishedTaskCount = $this->getUserTasks('Finished')->count();
        $finishedTeamTaskCount = $this->getUserTeamTasks('Finished')->count();
        return $finishedTaskCount + $finishedTeamTaskCount;
    }

    private function getOnProgressTaskCount()
    {
        $onProgressTaskCount = $this->getUserTasks('On Progress')->count();
        $onProgressTeamTaskCount = $this->getUserTeamTasks('On Progress')->count();
        return $onProgressTaskCount + $onProgressTeamTaskCount;
    }

    private function getToBeApprovedTaskCount()
    {
        $toBeApprovedTaskCount = $this->getUserTasks('To be Approved')->count();
        $toBeApprovedTeamTaskCount = $this->getUserTeamTasks('To be Approved')->count();
        return $toBeApprovedTaskCount + $toBeApprovedTeamTaskCount;
    }

    private function getLatestTasks()
    {
        $latestTasks = $this->getUserTasks()->latest()->take(4)->get();
        Log::info('Latest Tasks: '.$latestTasks);
        $latestTeamTasks = $this->getUserTeamTasks()->latest()->take(4)->get();
        Log::info('Latest Team Tasks: '. $latestTeamTasks);

        $combinedTasks = $latestTasks->concat($latestTeamTasks)->sortByDesc('created_at')->take(4);
        Log::info('Combined Tasks: '. $combinedTasks);

        return $combinedTasks;
    }

    private function getUserTasks($status = null)
    {
        $query = Task::where('user_id', Auth::id());
        if ($status) {
            $query->where('status', $status);
        }
        return $query;
    }

    private function getUserTeamTasks($status = null)
    {
        $query = TeamTask::whereHas('assignees', function ($query) {
            $query->where('user_id', Auth::id());
        });
        if ($status) {
            $query->where('status', $status);
        }
        return $query;
    }
}
