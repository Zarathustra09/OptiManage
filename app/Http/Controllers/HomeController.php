<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $employeeCount = $this->getEmployeeCount();
        $finishedTaskCount = $this->getFinishedTaskCount();
        $onProgressTaskCount = $this->getOnProgressTaskCount();
        $toBeApprovedTaskCount = $this->getToBeApprovedTaskCount();
        $latestTasks = Task::latest()->take(8)->get();
        $lowQuantityItems = $this->getLowQuantityItems();

        return view('home', compact('employeeCount', 'finishedTaskCount', 'onProgressTaskCount', 'toBeApprovedTaskCount', 'latestTasks', 'lowQuantityItems'));
    }

    /**
     * Get the count of users with role_id 1.
     *
     * @return int
     */
    private function getEmployeeCount()
    {
        return User::where('role_id', 0)->count();
    }

    /**
     * Get the count of tasks with status "Finished".
     *
     * @return int
     */
    private function getFinishedTaskCount()
    {
        return Task::where('status', 'Finished')->count();
    }

    /**
     * Get the count of tasks with status "On Progress".
     *
     * @return int
     */
    private function getOnProgressTaskCount()
    {
        return Task::where('status', 'On Progress')->count();
    }

    /**
     * Get the count of tasks with status "To be Approved".
     *
     * @return int
     */
    private function getToBeApprovedTaskCount()
    {
        return Task::where('status', 'To be Approved')->count();
    }

    private function getLowQuantityItems()
    {
        return Inventory::where('quantity', '<', 10)->get(); // Adjust the threshold as needed
    }
}
