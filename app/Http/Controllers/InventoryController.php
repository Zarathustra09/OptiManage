<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::all();
        return view('admin.inventory.index', compact('inventories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'description' => 'nullable|string',
        ]);

        Inventory::create($request->all());

        return response()->json(['success' => 'Inventory created successfully']);
    }

    public function show($id)
    {
        $inventory = Inventory::findOrFail($id);
        return response()->json($inventory);
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'description' => 'nullable|string',
        ]);

        $inventory->update($request->all());

        return response()->json(['success' => 'Inventory updated successfully']);
    }

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return response()->json(['success' => 'Inventory deleted successfully']);
    }
}
