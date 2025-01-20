<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_task_id', 'task_id', 'image_path'
    ];

    public function teamTask()
    {
        return $this->belongsTo(TeamTask::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
