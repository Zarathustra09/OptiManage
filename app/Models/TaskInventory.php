<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskInventory extends Model
{
    use HasFactory;

    protected $table = 'task_inventory';

    protected $fillable = ['task_id', 'inventory_id', 'quantity'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
