<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnedItem extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'team_task_id', 'inventory_id', 'quantity', 'returned_at'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function teamTask()
    {
        return $this->belongsTo(TeamTask::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
