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
        $statuses = config('status.statuses');
        $tasks = TeamTask::with('assignees')->get();
        $categories = TaskCategory::all();
        return view('admin.teamTask.index', compact('tasks', 'categories', 'statuses'));
    }

    public function create()
    {
        $statuses = config('status.statuses');
        $categories = TaskCategory::all(); // Add this line
        return view('admin.teamTask.create', compact('categories', 'statuses')); // Add this line
    }

    private function checkTicketIdExists($ticketId)
    {
        return TeamTask::where('ticket_id', $ticketId)->exists();
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
            'inventory_items' => 'required|json',
            'task_category_id' => 'required|exists:task_categories,id',
            'ticket_id' => 'required|string|max:255|unique:team_tasks,ticket_id',
        ]);

        Log::info('Validation passed');

        // Check if the ticket ID already exists
        if ($this->checkTicketIdExists($request->ticket_id)) {
            Log::warning('Ticket ID already exists', ['ticket_id' => $request->ticket_id]);
            return redirect()->back()->with('error', 'The ticket ID already exists. Please use a different Ticket ID.');
        }

        $data = $request->all();

        $teamTask = TeamTask::create($data);
        Log::info('Team task created', ['team_task_id' => $teamTask->id]);

        $inventoryItems = json_decode($request->inventory_items, true);

        // Combine overlapping inventory items
        $combinedInventoryItems = [];
        foreach ($inventoryItems as $item) {
            if (isset($combinedInventoryItems[$item['inventory_id']])) {
                $combinedInventoryItems[$item['inventory_id']]['quantity'] += $item['quantity'];
            } else {
                $combinedInventoryItems[$item['inventory_id']] = $item;
            }
        }

        foreach ($combinedInventoryItems as $item) {
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


    public function updateAdmin(Request $request, $id)
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

        return response()->json(['success' => 'Team Task updated successfully.'], 200);
    }

    public function update(Request $request, $id)
    {
        Log::info('Update function called', ['request' => $request->all(), 'team_task_id' => $id]);

        $request->validate([
            'status' => 'required|string|in:To be Approved,Checked,On Progress,Finished,Cancel',
        ]);

        Log::info('Validation passed');

        $task = TeamTask::findOrFail($id);
        $task->update(['status' => $request->status]);

        Log::info('Team task status updated', ['team_task_id' => $task->id, 'status' => $task->status]);

        return response()->json(['success' => 'Team Task status has been updated successfully.'], 200);
    }
    public function show($id)
    {
        $teamTask = TeamTask::with(['assignees.user', 'inventories', 'images'])->findOrFail($id);
        $employees = User::where('role_id', 0)->get(['id', 'name']);
        return view('admin.teamTask.show', compact('teamTask', 'employees'));
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


    public function addInventoryItem(Request $request)
    {
        $request->validate([
            'team_task_id' => 'required|exists:team_tasks,id',
            'inventory_id' => 'required|exists:inventories,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $teamTask = TeamTask::findOrFail($request->team_task_id);
        $inventory = Inventory::findOrFail($request->inventory_id);

        if ($inventory->quantity < $request->quantity) {
            return response()->json(['error' => 'Selected inventory is out of stock.'], 400);
        }

        $existingItem = $teamTask->inventories()->where('inventory_id', $request->inventory_id)->first();

        if ($existingItem) {
            // Update the quantity of the existing item
            $newQuantity = $existingItem->pivot->quantity + $request->quantity;
            $teamTask->inventories()->updateExistingPivot($request->inventory_id, ['quantity' => $newQuantity]);
        } else {
            // Attach the new item
            $teamTask->inventories()->attach($inventory->id, ['quantity' => $request->quantity]);
        }

        // Deduct the inventory quantity
        $inventory->quantity -= $request->quantity;
        $inventory->save();

        return response()->json(['success' => 'Inventory item added successfully.']);
    }

    public function removeInventoryItem(Request $request, $id)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
        ]);

        $teamTask = TeamTask::findOrFail($id);
        $inventory = Inventory::findOrFail($request->inventory_id);

        $pivotRow = $teamTask->inventories()->where('inventory_id', $inventory->id)->first();
        if ($pivotRow) {
            $inventory->quantity += $pivotRow->pivot->quantity;
            $inventory->save();

            $teamTask->inventories()->detach($inventory->id);

            return response()->json(['success' => 'Inventory item removed successfully.']);
        }

        return response()->json(['error' => 'Inventory item not found.'], 404);
    }
}
