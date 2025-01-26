<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\TaskInventory;
use App\Models\TeamTaskInventory;
use App\Models\Inventory;
use App\Models\ReturnedItem;

class EmployeeItemController extends Controller
{

    public function returnSingle($teamTaskId, $inventoryId, $quantity)
    {
        $item = TeamTaskInventory::where('team_task_id', $teamTaskId)
            ->where('inventory_id', $inventoryId)
            ->first();

        if ($item && $item->quantity >= $quantity) {
            $inventory = Inventory::find($item->inventory_id);
            if ($inventory) {
                $inventory->quantity += $quantity;
                $inventory->save();
            }

            ReturnedItem::create([
                'team_task_id' => $teamTaskId,
                'inventory_id' => $item->inventory_id,
                'quantity' => $quantity,
                'returned_at' => now(),
            ]);

            $item->quantity -= $quantity;
            if ($item->quantity == 0) {
                $item->delete();
            } else {
                $item->save();
            }
        }

        return response()->json(['success' => 'Item(s) returned to inventory successfully.']);
    }

    public function returnAll($teamTaskId)
    {
        $items = TeamTaskInventory::where('team_task_id', $teamTaskId)->get();

        foreach ($items as $item) {
            $inventory = Inventory::find($item->inventory_id);
            if ($inventory) {
                $inventory->quantity += $item->quantity;
                $inventory->save();
            }

            ReturnedItem::create([
                'team_task_id' => $teamTaskId,
                'inventory_id' => $item->inventory_id,
                'quantity' => $item->quantity,
                'returned_at' => now(),
            ]);

            $item->delete();
        }

        return response()->json(['success' => 'All items returned to inventory successfully.']);
    }

    public function returnSingleTaskItem($taskId, $inventoryId, $quantity)
    {
        $item = TaskInventory::where('task_id', $taskId)
            ->where('inventory_id', $inventoryId)
            ->first();

        if ($item && $item->quantity >= $quantity) {
            $inventory = Inventory::find($item->inventory_id);
            if ($inventory) {
                $inventory->quantity += $quantity;
                $inventory->save();
            }

            ReturnedItem::create([
                'task_id' => $taskId, // Save task_id in team_task_id field
                'inventory_id' => $item->inventory_id,
                'quantity' => $quantity,
                'returned_at' => now(),
            ]);

            $item->quantity -= $quantity;
            if ($item->quantity == 0) {
                $item->delete();
            } else {
                $item->save();
            }
        }

        return response()->json(['success' => 'Item(s) returned to inventory successfully.']);
    }

    public function returnAllTaskItems($taskId)
    {
        $items = TaskInventory::where('task_id', $taskId)->get();

        foreach ($items as $item) {
            $inventory = Inventory::find($item->inventory_id);
            if ($inventory) {
                $inventory->quantity += $item->quantity;
                $inventory->save();
            }

            ReturnedItem::create([
                'task_id' => $taskId, // Save task_id in team_task_id field
                'inventory_id' => $item->inventory_id,
                'quantity' => $item->quantity,
                'returned_at' => now(),
            ]);

            $item->delete();
        }

        return response()->json(['success' => 'All items returned to inventory successfully.']);
    }
}
