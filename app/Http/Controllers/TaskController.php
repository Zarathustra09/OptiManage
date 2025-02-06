<?php

namespace App\Http\Controllers;

use App\Mail\TaskAssigned;
use App\Models\Inventory;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\TaskInventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        $categories = TaskCategory::all();
        return view('admin.task.index', compact('tasks', 'categories')); // Pass categories to the view
    }

    public function create()
    {
        $users = User::where('role_id', 0)->get();
        $inventories = Inventory::all();
        $categories = TaskCategory::all(); // Add this line
        return view('admin.task.create', compact('users', 'inventories', 'categories'));
    }

    public function store(Request $request)
    {
        Log::info('Store function called', ['request' => $request->all()]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string|in:To be Approved,On Progress,Finished,Cancel',
            'user_id' => 'required|exists:users,id',
            'task_category_id' => 'required|exists:task_categories,id',
            'start_date' => 'required|date_format:Y-m-d\TH:i',
            'end_date' => 'required|date_format:Y-m-d\TH:i|after:start_date',
        ]);

        Log::info('Validation passed');

        $taskStart = new \DateTime($request->start_date);
        $taskEnd = new \DateTime($request->end_date);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'user_id' => $request->user_id,
            'task_category_id' => $request->task_category_id,
            'start_date' => $taskStart,
            'end_date' => $taskEnd,
            'ticket_id' => $request->ticket_id,
        ]);

        Log::info('Task created', ['task_id' => $task->id]);

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
                return response()->json(['error' => 'Selected inventory is out of stock.'], 400);
            }

            // Deduct inventory quantity
            $inventory->quantity -= $item['quantity'];
            $inventory->save();
            Log::info('Inventory quantity updated', ['inventory_id' => $inventory->id, 'new_quantity' => $inventory->quantity]);

            // Attach inventory to task
            $task->inventories()->syncWithoutDetaching([$inventory->id => ['quantity' => $item['quantity']]]);
            Log::info('Inventory attached to task', ['task_id' => $task->id, 'inventory_id' => $inventory->id, 'quantity' => $item['quantity']]);
        }

        $user = User::findOrFail($request->user_id);
        Mail::to($user->email)->send(new TaskAssigned($task));

        Log::info('Task creation process completed successfully', ['task_id' => $task->id]);
        return redirect()->route('admin.task.index')->with('success', 'Task has been created successfully.');
    }

    public function updateAdmin(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:To be Approved,On Progress,Finished,Cancel',
            'task_category_id' => 'required|exists:task_categories,id',
            'start_date' => 'nullable|date_format:Y-m-d\TH:i',
            'end_date' => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:start_date',
        ]);

        $task = Task::findOrFail($id);
        $task->update($request->only(['title', 'description', 'status', 'task_category_id', 'start_date', 'end_date']));

        return response()->json(['success' => 'Task has been updated successfully.'], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:To be Approved,On Progress,Finished,Cancel',
        ]);

        $task = Task::findOrFail($id);
        $task->update(['status' => $request->status]);

        return response()->json(['success' => 'Task status has been updated successfully.']);
    }

    public function show($id)
    {
        $task = Task::with(['user', 'inventories', 'images'])->findOrFail($id);
        return view('admin.task.show', compact('task'));
    }

    public function showSingle($id)
    {
        $task = Task::with(['user', 'inventories', 'images'])->findOrFail($id);
        return response()->json($task);
    }


    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['success' => 'Task has been deleted successfully.']);
    }

    public function acceptTask($id)
    {
        $task = Task::findOrFail($id);
        $task->status = 'On Progress';
        $task->save();

        return response()->json(['success' => 'Task has been accepted successfully.']);
    }


    public function addInventoryItem(Request $request)
    {
        Log::info('Add inventory item function called', ['request' => $request->all()]);

        $validator = \Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id',
            'inventory_id' => 'required|exists:inventories,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return response()->json(['error' => 'Validation failed', 'messages' => $validator->errors()], 422);
        }

        $task = Task::findOrFail($request->task_id);
        $inventory = Inventory::findOrFail($request->inventory_id);

        if ($inventory->quantity < $request->quantity) {
            return response()->json(['error' => 'Selected inventory is out of stock.'], 400);
        }

        $existingItem = $task->inventories()->where('inventory_id', $request->inventory_id)->first();

        if ($existingItem) {
            // Update the quantity of the existing item
            $newQuantity = $existingItem->pivot->quantity + $request->quantity;
            $task->inventories()->updateExistingPivot($request->inventory_id, ['quantity' => $newQuantity]);
        } else {
            // Attach the new item
            $task->inventories()->attach($inventory->id, ['quantity' => $request->quantity]);
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

        $teamTask = Task::findOrFail($id);
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
