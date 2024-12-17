<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TeamTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'status', 'ticket_id', 'start_date', 'end_date', 'proof_of_work', 'task_category_id'
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

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

//    protected static $logAttributes = ['*'];
//    protected static $logName = 'team_task';
//
//    public function getActivitylogOptions(): LogOptions
//    {
//        return LogOptions::defaults()
//            ->logOnly(['title', 'description', 'status', 'ticket_id', 'start_date', 'end_date', 'proof_of_work', 'task_category_id']);
//    }
}
