<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('admin.task.index', compact('tasks'));
    }

    public function create()
    {
        $users = User::where('role_id', 0)->get();
        $inventories = Inventory::all();
        return view('admin.task.create', compact('users', 'inventories'));
    }

    public function store(Request $request)
    {
        Log::info('Store function called', ['request' => $request->all()]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:To be Approved,On Progress,Finished,Cancel',
            'user_id' => 'required|exists:users,id',
            'inventory_items' => 'required|json',
            'start_date' => 'nullable|date_format:Y-m-d\TH:i',
            'end_date' => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:start_date',
            'proof_of_work' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        Log::info('Validation passed');

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
            return redirect()->back()->with('error', 'The task overlaps with an existing task.');
        }

        $ticket_id = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5)) . '-' . substr(str_shuffle('0123456789'), 0, 5);
        Log::info('Generated ticket ID', ['ticket_id' => $ticket_id]);

        if ($request->hasFile('proof_of_work')) {
            $proofOfWorkPath = $request->file('proof_of_work')->store('proof_of_work', 'public');
        }

        $task = Task::create(array_merge(
            $request->only(['title', 'description', 'status', 'user_id', 'start_date', 'end_date']),
            ['ticket_id' => $ticket_id, 'proof_of_work' => $proofOfWorkPath ?? null]
        ));

        Log::info('Task created', ['task_id' => $task->id]);

        $inventoryItems = json_decode($request->inventory_items, true);

        foreach ($inventoryItems as $item) {
            $inventory = Inventory::findOrFail($item['inventory_id']);
            if ($inventory->quantity < $item['quantity']) {
                Log::error('Inventory out of stock', ['inventory_id' => $item['inventory_id'], 'requested_quantity' => $item['quantity'], 'available_quantity' => $inventory->quantity]);
                return redirect()->back()->with('error', 'Selected inventory is out of stock.');
            }

            // Deduct inventory quantity
            $inventory->quantity -= $item['quantity'];
            $inventory->save();
            Log::info('Inventory quantity updated', ['inventory_id' => $inventory->id, 'new_quantity' => $inventory->quantity]);

            // Attach inventory to task
            $task->inventories()->attach($inventory->id, ['quantity' => $item['quantity']]);
            Log::info('Inventory attached to task', ['task_id' => $task->id, 'inventory_id' => $inventory->id, 'quantity' => $item['quantity']]);
        }

        Log::info('Task creation process completed successfully', ['task_id' => $task->id]);
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
            'start_date' => 'nullable|date_format:Y-m-d\TH:i',
            'end_date' => 'nullable|date_format:Y-m-d\TH:i|after_or_equal:start_date',
        ]);

        $overlappingTasks = Task::where('user_id', $request->user_id)
            ->where('id', '!=', $id)
            ->whereNotIn('status', ['Finished', 'Cancel'])
            ->where(function($query) use ($request) {
                if ($request->start_date && $request->end_date) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                        ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                        ->orWhere(function($query) use ($request) {
                            $query->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                        });
                }
            })->exists();

        if ($overlappingTasks) {
            return response()->json(['error' => 'The task overlaps with an existing task.'], 422);
        }

        $task = Task::findOrFail($id);

        if ($request->hasFile('proof_of_work')) {
            $proofOfWorkPath = $request->file('proof_of_work')->store('proof_of_work', 'public');
            $task->proof_of_work = $proofOfWorkPath;
        }

        $task->update($request->only(['title', 'description', 'status', 'start_date', 'end_date']));

        return response()->json(['success' => 'Task has been updated successfully.']);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['success' => 'Task has been deleted successfully.']);
    }
}
