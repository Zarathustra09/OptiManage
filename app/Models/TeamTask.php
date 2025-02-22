<?php

// app/Models/TeamTask.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'status', 'ticket_id', 'start_date', 'end_date', 'task_category_id', 'team_id', 'area_id',
        'cust_account_number', 'cust_name', 'cust_type', 'cus_telephone', 'cus_email', 'cus_address', 'cus_landmark'
    ];

    protected $casts = [
        'start_date' => 'datetime:Y-m-d H:i:s',
        'end_date' => 'datetime:Y-m-d H:i:s',
    ];

    public function assignees()
    {
        return $this->hasManyThrough(TeamAssignee::class, Team::class, 'id', 'team_id', 'team_id', 'id');
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, 'team_task_inventory')->withPivot('quantity');
    }

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function images()
    {
        return $this->hasMany(TaskImage::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
