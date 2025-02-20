<?php

// app/Models/Team.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'area_id'];

    public function assignees()
    {
        return $this->hasMany(TeamAssignee::class);
    }

    public function tasks()
    {
        return $this->hasMany(TeamTask::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
