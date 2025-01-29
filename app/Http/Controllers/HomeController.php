<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\TeamTask;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $employeeCount = $this->getEmployeeCount();
        $finishedTaskCount = $this->getFinishedTaskCount();
        $onProgressTaskCount = $this->getOnProgressTaskCount();
        $toBeApprovedTaskCount = $this->getToBeApprovedTaskCount();
        $latestTasks = $this->getLatestTasks();
        $lowQuantityItems = $this->getLowQuantityItems();

        return view('home', compact('employeeCount', 'finishedTaskCount', 'onProgressTaskCount', 'toBeApprovedTaskCount', 'latestTasks', 'lowQuantityItems'));
    }

    private function getEmployeeCount()
    {
        return User::where('role_id', 0)->count();
    }

    private function getFinishedTaskCount()
    {
        $finishedTaskCount = $this->getAllTasks('Finished')->count();
        $finishedTeamTaskCount = $this->getAllTeamTasks('Finished')->count();
        return $finishedTaskCount + $finishedTeamTaskCount;
    }

    private function getOnProgressTaskCount()
    {
        $onProgressTaskCount = $this->getAllTasks('On Progress')->count();
        $onProgressTeamTaskCount = $this->getAllTeamTasks('On Progress')->count();
        return $onProgressTaskCount + $onProgressTeamTaskCount;
    }

    private function getToBeApprovedTaskCount()
    {
        $toBeApprovedTaskCount = $this->getAllTasks('To be Approved')->count();
        $toBeApprovedTeamTaskCount = $this->getAllTeamTasks('To be Approved')->count();
        return $toBeApprovedTaskCount + $toBeApprovedTeamTaskCount;
    }

    private function getLatestTasks()
    {
        $latestTasks = $this->getAllTasks()->latest()->take(4)->get();
        Log::info('Latest Tasks: '.$latestTasks);
        $latestTeamTasks = $this->getAllTeamTasks()->latest()->take(4)->get();
        Log::info('Latest Team Tasks: '. $latestTeamTasks);

        $combinedTasks = $latestTasks->concat($latestTeamTasks)->sortByDesc('created_at')->take(4);
        Log::info('Combined Tasks: '. $combinedTasks);

        return $combinedTasks;
    }

    private function getAllTasks($status = null)
    {
        $query = Task::query();
        if ($status) {
            $query->where('status', $status);
        }
        return $query;
    }

    private function getAllTeamTasks($status = null)
    {
        $query = TeamTask::query();
        if ($status) {
            $query->where('status', $status);
        }
        return $query;
    }

    private function getLowQuantityItems()
    {
        return Inventory::where('quantity', '<', 10)->get();
    }
}
