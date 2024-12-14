<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'status', 'ticket_id', 'start_date', 'end_date', 'proof_of_work'
    ];

    protected $casts = [
        'start_date' => 'datetime:Y-m-d H:i:s',
        'end_date' => 'datetime:Y-m-d H:i:s',
    ];

    public function assignees()
    {
        return $this->hasMany(TeamAssignee::class);
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, 'team_task_inventory')->withPivot('quantity');
    }
}
