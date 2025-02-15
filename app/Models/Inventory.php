<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Inventory extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['category_id', 'sku', 'name', 'quantity', 'description'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_inventory');
    }

    public function defects()
    {
        return $this->hasMany(Defect::class);
    }

    protected static $logAttributes = ['*'];
    protected static $logName = 'inventory';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['category_id', 'sku', 'name', 'quantity', 'description']);
    }
}
