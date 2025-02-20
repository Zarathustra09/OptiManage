<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'task_category_id', 'title', 'description', 'status', 'ticket_id', 'start_date', 'end_date', 'area_id'
    ];

    protected $casts = [
        'start_date' => 'datetime:Y-m-d H:i:s',
        'end_date' => 'datetime:Y-m-d H:i:s',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, 'task_inventory')->withPivot('quantity');
    }

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function images()
    {
        return $this->hasMany(TaskImage::class);
    }

    // app/Models/User.php

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
