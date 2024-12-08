<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'status', 'ticket_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, 'task_inventory');
    }
}
