<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamAssignee extends Model
{
    use HasFactory;

    protected $fillable = ['team_task_id', 'user_id'];

    public function teamTask()
    {
        return $this->belongsTo(TeamTask::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
