<?php

// app/Http/Controllers/DefectController.php

namespace App\Http\Controllers;

use App\Models\Defect;
use App\Models\Inventory;
use Illuminate\Http\Request;

class DefectController extends Controller
{
    public function index()
    {
        $defects = Defect::all();
        return view('admin.defect.index', compact('defects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $inventory = Inventory::findOrFail($request->inventory_id);
        if ($inventory->quantity < $request->quantity) {
            return response()->json(['error' => 'Not enough inventory available'], 422);
        }

        $inventory->decrement('quantity', $request->quantity);
        Defect::create($request->all());

        return response()->json(['success' => 'Defect created successfully']);
    }

    public function show($id)
    {
        $defect = Defect::with('inventory')->findOrFail($id);
        return response()->json($defect);
    }

    public function update(Request $request, $id)
    {
        $defect = Defect::findOrFail($id);
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $inventory = Inventory::findOrFail($request->inventory_id);
        $quantityDifference = $request->quantity - $defect->quantity;

        if ($quantityDifference > 0 && $inventory->quantity < $quantityDifference) {
            return response()->json(['error' => 'Not enough inventory available'], 422);
        }

        $inventory->decrement('quantity', $quantityDifference);
        $defect->update($request->all());

        return response()->json(['success' => 'Defect updated successfully']);
    }

    public function destroy($id)
    {
        $defect = Defect::findOrFail($id);
        $defect->delete();

        return response()->json(['success' => 'Defect deleted successfully']);
    }
}
