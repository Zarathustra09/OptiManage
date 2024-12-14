<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamTaskInventory extends Model
{
    use HasFactory;

    protected $table = 'team_task_inventory';

    protected $fillable = ['team_task_id', 'inventory_id', 'quantity'];

    public function teamTask()
    {
        return $this->belongsTo(TeamTask::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
