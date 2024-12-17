<?php

namespace App\Http\Controllers;

use App\Models\TaskCategory;
use Illuminate\Http\Request;

class TaskCategoryController extends Controller
{
    public function list()
    {
        $categories = TaskCategory::all();
        return response()->json($categories);
    }

    public function index()
    {
        $categories = TaskCategory::all();
        return view('admin.taskCategory.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:task_categories',
            'description' => 'nullable|string',
        ]);

        TaskCategory::create($request->all());

        return response()->json(['success' => 'Category created successfully']);
    }

    public function show($id)
    {
        $category = TaskCategory::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = TaskCategory::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:task_categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return response()->json(['success' => 'Category updated successfully']);
    }

    public function destroy($id)
    {
        $category = TaskCategory::findOrFail($id);
        $category->delete();

        return response()->json(['success' => 'Category deleted successfully']);
    }
}
