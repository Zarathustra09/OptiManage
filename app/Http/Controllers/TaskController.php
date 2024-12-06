<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('admin.task.index', compact('tasks'));
    }

    public function create()
    {
        $users = User::all();
        $inventories = Inventory::all();
        return view('admin.task.create', compact('users', 'inventories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:To be Approved,On Progress,Finished,Cancel',
            'user_id' => 'required|exists:users,id',
            'inventory_items' => 'required|json',
        ]);

        $ticket_id = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5)) . '-' . substr(str_shuffle('0123456789'), 0, 5);

        $task = Task::create(array_merge(
            $request->only(['title', 'description', 'status', 'user_id']),
            ['ticket_id' => $ticket_id]
        ));

        $inventoryItems = json_decode($request->inventory_items, true);

        foreach ($inventoryItems as $item) {
            $inventory = Inventory::findOrFail($item['inventory_id']);
            if ($inventory->quantity < $item['quantity']) {
                return redirect()->back()->with('error', 'Selected inventory is out of stock.');
            }

            // Deduct inventory quantity
            $inventory->quantity -= $item['quantity'];
            $inventory->save();

            // Attach inventory to task
            $task->inventories()->attach($inventory->id, ['quantity' => $item['quantity']]);
        }

        return redirect()->route('admin.task.index')->with('success', 'Task has been created successfully.');
    }

    public function show($id)
    {
        $task = Task::findOrFail($id);
        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:To be Approved,On Progress,Finished,Cancel',
        ]);

        $task = Task::findOrFail($id);
        $task->update($request->all());

        return response()->json(['success' => 'Task has been updated successfully.']);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['success' => 'Task has been deleted successfully.']);
    }
}
