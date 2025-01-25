<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TeamTask;
use App\Models\TaskImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function storeTaskImage(Request $request, $taskId)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $task = Task::findOrFail($taskId);

        $filePath = $request->file('image')->store('task_images', 'public');

        TaskImage::create([
            'task_id' => $task->id,
            'image_path' => $filePath,
        ]);

        return response()->json(['success' => 'Task image uploaded successfully.']);
    }

    public function destroyImage($id)
    {
        $image = TaskImage::findOrFail($id);

        // Delete the image file from storage
        Storage::delete('public/' . $image->image_path);

        // Delete the image record from the database
        $image->delete();

        return response()->json(['success' => 'Image deleted successfully.']);
    }

    public function storeTeamTaskImage(Request $request, $teamTaskId)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $teamTask = TeamTask::findOrFail($teamTaskId);

        $filePath = $request->file('image')->store('team_task_images', 'public');

        TaskImage::create([
            'team_task_id' => $teamTask->id,
            'image_path' => $filePath,
        ]);

        return response()->json(['success' => 'Team task image uploaded successfully.']);
    }

    public function destroyTeamTaskImage($id)
    {
        $image = TaskImage::findOrFail($id);

        // Delete the image file from storage
        Storage::delete('public/' . $image->image_path);

        // Delete the image record from the database
        $image->delete();

        return response()->json(['success' => 'Image deleted successfully.']);
    }
}
