<?php

namespace App\Http\Controllers;

use App\Mail\TaskAssigned;
use App\Models\Area;
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
        $statuses = config('status.statuses');
        return view('admin.task.index', compact('tasks', 'categories', 'statuses')); // Pass categories to the view
    }

    public function create()
    {
        Log::info('Create function called');
        $statuses = config('status.statuses');
        $users = User::where('role_id', 0)->get();
        $inventories = Inventory::all();
        $categories = TaskCategory::all();
        $areas = Area::all(); // Add this line
        return view('admin.task.create', compact('users', 'inventories', 'categories', 'statuses', 'areas'));
    }

    public function store(Request $request)
    {
        Log::info('Store function called', ['request' => $request->all()]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string|in:To be Approved,Checked,On Progress,Finished,Cancel',
            'user_id' => 'required|exists:users,id',
            'task_category_id' => 'required|exists:task_categories,id',
            'start_date' => 'required|date_format:Y-m-d\TH:i',
            'end_date' => 'required|date_format:Y-m-d\TH:i|after:start_date',
            'ticket_id' => 'required|string|unique:tasks,ticket_id',
            'area_id' => 'required|exists:areas,id',
            'inventory_items' => 'required|json',
            'cust_account_number' => 'nullable|string|max:255',
            'cust_name' => 'nullable|string|max:255',
            'cust_type' => 'nullable|string|max:255',
            'cus_telephone' => 'nullable|string|max:255',
            'cus_email' => 'nullable|string|email|max:255',
            'cus_address' => 'nullable|string',
            'cus_landmark' => 'nullable|string|max:255',
        ]);

        Log::info('Validation passed');

        if ($this->checkTicketIdExists($request->ticket_id)) {
            Log::warning('Ticket ID already exists', ['ticket_id' => $request->ticket_id]);
            return redirect()->back()->with('error', 'The ticket ID already exists. Please use a different Ticket ID.');
        }

        $overlappingTasks = Task::where('user_id', $request->user_id)
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
            Log::warning('Overlapping task detected', ['user_id' => $request->user_id, 'start_date' => $request->start_date, 'end_date' => $request->end_date]);
            return redirect()->back()->withInput()->with('error', 'The task overlaps with an existing task.');
        }

        $task = Task::create($request->only([
            'title', 'description', 'status', 'user_id', 'task_category_id', 'start_date', 'end_date', 'ticket_id', 'area_id',
            'cust_account_number', 'cust_name', 'cust_type', 'cus_telephone', 'cus_email', 'cus_address', 'cus_landmark'
        ]));

        Log::info('Task created', ['task_id' => $task->id]);

        $inventoryItems = json_decode($request->inventory_items, true);

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

            $inventory->quantity -= $item['quantity'];
            $inventory->save();
            Log::info('Inventory quantity updated', ['inventory_id' => $inventory->id, 'new_quantity' => $inventory->quantity]);

            $task->inventories()->syncWithoutDetaching([$inventory->id => ['quantity' => $item['quantity']]]);
            Log::info('Inventory attached to task', ['task_id' => $task->id, 'inventory_id' => $inventory->id, 'quantity' => $item['quantity']]);
        }

        $user = User::findOrFail($request->user_id);
        Mail::to($user->email)->send(new TaskAssigned($task));

        Log::info('Task creation process completed successfully', ['task_id' => $task->id]);
        return redirect()->route('admin.task.index')->with('success', 'Task has been created successfully.');
    }

    private function checkTicketIdExists($ticketId)
    {
        return Task::where('ticket_id', $ticketId)->exists();
    }


    public function updateAdmin(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:To be Approved,Checked,On Progress,Finished,Cancel',
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
            'status' => 'required|string|in:To be Approved,Checked,On Progress,Finished,Cancel',
        ]);

        $task = Task::findOrFail($id);
        $task->update(['status' => $request->status]);

        return response()->json(['success' => 'Task status has been updated successfully.']);
    }

    public function updateCustomer(Request $request, $id)
    {
        $request->validate([
            'cust_account_number' => 'nullable|string|max:255',
            'cust_name' => 'nullable|string|max:255',
            'cust_type' => 'nullable|string|max:255',
            'cus_telephone' => 'nullable|string|max:255',
            'cus_email' => 'nullable|string|email|max:255',
            'cus_address' => 'nullable|string',
            'cus_landmark' => 'nullable|string|max:255',
        ]);

        $task = Task::findOrFail($id);
        $task->update($request->only([
            'cust_account_number', 'cust_name', 'cust_type', 'cus_telephone', 'cus_email', 'cus_address', 'cus_landmark'
        ]));

        return response()->json(['success' => 'Customer details updated successfully.']);
    }



    public function show($id)
    {
        $task = Task::with(['user', 'inventories', 'images', 'area'])->findOrFail($id);
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
