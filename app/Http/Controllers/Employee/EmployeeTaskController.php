<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeTaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('user_id', Auth::id())->get();
        $categories = TaskCategory::all();
        return view('employee.task.index', compact('tasks', 'categories'));
    }

    public function create()
    {
        $inventories = Inventory::all();
        $categories = TaskCategory::all();
        return view('employee.task.create', compact('inventories', 'categories'));
    }

    public function store(Request $request)
    {
        Log::info('Store function called', ['request' => $request->all()]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_category_id' => 'required|exists:task_categories,id',
            'inventory_items' => 'required|json',
            'start_date' => 'nullable|date_format:Y-m-d\TH:i',
            'end_date' => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:start_date',
        ]);

        Log::info('Validation passed');

        $overlappingTasks = Task::where('user_id', Auth::id())
            ->whereNotIn('status', ['Finished', 'Cancel'])
            ->where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function($query) use ($request) {
                        $query->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })->exists();

        if ($overlappingTasks) {
            Log::warning('Overlapping task detected', ['user_id' => Auth::id(), 'start_date' => $request->start_date, 'end_date' => $request->end_date]);
            return redirect()->back()->with('error', 'The task overlaps with an existing task.');
        }

        $ticket_id = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5)) . '-' . substr(str_shuffle('0123456789'), 0, 5);
        Log::info('Generated ticket ID', ['ticket_id' => $ticket_id]);

        $task = Task::create(array_merge(
            $request->only(['title', 'description', 'task_category_id', 'start_date', 'end_date']),
            ['ticket_id' => $ticket_id, 'status' => 'To be Approved', 'user_id' => Auth::id()]
        ));

        Log::info('Task created', ['task_id' => $task->id]);

        $inventoryItems = json_decode($request->inventory_items, true);

        foreach ($inventoryItems as $item) {
            $task->inventories()->attach($item['inventory_id'], ['quantity' => $item['quantity']]);
            Log::info('Inventory attached to task', ['task_id' => $task->id, 'inventory_id' => $item['inventory_id'], 'quantity' => $item['quantity']]);
        }

        Log::info('Task creation process completed successfully', ['task_id' => $task->id]);
        return redirect()->route('employee.task.index')->with('success', 'Task has been created successfully.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:To be Approved,Checked,On Progress,Finished,Cancel',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'task_category_id' => 'nullable|exists:task_categories,id',
            'start_date' => 'nullable|date_format:Y-m-d\TH:i',
            'end_date' => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:start_date',
        ]);

        $task = Task::where('user_id', Auth::id())->findOrFail($id);

        $task->update($request->only(['title', 'description', 'status', 'task_category_id', 'start_date', 'end_date']));

        return response()->json(['success' => 'Task updated successfully.']);
    }

    public function show($id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($task);
    }

    public function showSingle($id)
    {
        $task = Task::with('inventories')->findOrFail($id);
        return view('employee.task.show', compact('task'));
    }

    public function destroy($id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        $task->delete();

        return response()->json(['success' => 'Task has been deleted successfully.']);
    }

}
