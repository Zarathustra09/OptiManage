<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\TeamTask;
use App\Models\TaskCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeamTaskController extends Controller
{
    public function index()
    {
        $tasks = TeamTask::with('assignees')->get();
        return view('admin.teamTask.index', compact('tasks'));
    }

    public function create()
    {
        $categories = TaskCategory::all(); // Add this line
        return view('admin.teamTask.create', compact('categories')); // Add this line
    }

    public function store(Request $request)
    {
        Log::info('Store function called', ['request' => $request->all()]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'proof_of_work' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'inventory_items' => 'required|json',
            'task_category_id' => 'required|exists:task_categories,id',
        ]);

        Log::info('Validation passed');

        $prefix = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5));
        $suffix = substr(str_shuffle('0123456789'), 0, 5);
        $ticket_id = $prefix . '-' . $suffix;
        Log::info('Generated ticket ID', ['ticket_id' => $ticket_id]);

        $data = $request->all();
        if ($request->hasFile('proof_of_work')) {
            $file = $request->file('proof_of_work');
            $path = $file->store('proof_of_work', 'public');
            $data['proof_of_work'] = $path;
            Log::info('Proof of work uploaded', ['path' => $path]);
        }

        $teamTask = TeamTask::create(array_merge($data, ['ticket_id' => $ticket_id]));
        Log::info('Team task created', ['team_task_id' => $teamTask->id]);

        $inventoryItems = json_decode($request->inventory_items, true);

        foreach ($inventoryItems as $item) {
            $inventory = Inventory::findOrFail($item['inventory_id']);
            if ($inventory->quantity < $item['quantity']) {
                Log::error('Inventory out of stock', ['inventory_id' => $item['inventory_id'], 'requested_quantity' => $item['quantity'], 'available_quantity' => $inventory->quantity]);
                return redirect()->back()->with('error', 'Selected inventory is out of stock.');
            }

            $inventory->quantity -= $item['quantity'];
            $inventory->save();
            Log::info('Inventory quantity updated', ['inventory_id' => $inventory->id, 'new_quantity' => $inventory->quantity]);

            $teamTask->inventories()->attach($inventory->id, ['quantity' => $item['quantity']]);
            Log::info('Inventory attached to team task', ['team_task_id' => $teamTask->id, 'inventory_id' => $inventory->id, 'quantity' => $item['quantity']]);
        }

        Log::info('Team task creation process completed successfully', ['team_task_id' => $teamTask->id]);
        return redirect()->route('admin.teamTask.index')->with('success', 'Team Task created successfully.');
    }

    public function update(Request $request, $id)
    {
        Log::info('Update function called', ['request' => $request->all(), 'team_task_id' => $id]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'proof_of_work' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'task_category_id' => 'required|exists:task_categories,id',
        ]);

        Log::info('Validation passed');

        $task = TeamTask::findOrFail($id);
        $data = $request->all();
        if ($request->hasFile('proof_of_work')) {
            $file = $request->file('proof_of_work');
            $path = $file->store('proof_of_work', 'public');
            $data['proof_of_work'] = $path;
            Log::info('Proof of work uploaded', ['path' => $path]);
        }

        $task->update($data);
        Log::info('Team task updated', ['team_task_id' => $task->id]);

        return redirect()->route('admin.teamTask.index')->with('success', 'Team Task updated successfully.');
    }
    public function show($id)
    {
        $task = TeamTask::with(['assignees.user', 'inventories'])->findOrFail($id);
        $employees = User::where('role_id', 0)->get(['id', 'name']);
        return view('admin.teamTask.show', compact('task', 'employees'));
    }

    public function showSingle($id)
    {
        $task = TeamTask::findOrFail($id);
        return response()->json($task);
    }

    public function edit($id)
    {
        $task = TeamTask::findOrFail($id);
        $categories = TaskCategory::all(); // Add this line
        return view('admin.teamTask.edit', compact('task', 'categories')); // Add this line
    }



    public function destroy($id)
    {
        $task = TeamTask::findOrFail($id);
        $task->delete();

        return response()->json(['success' => 'Team Task has been deleted successfully.']);
    }
}
