<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamTask extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status'];

    public function assignees()
    {
        return $this->hasMany(TeamAssignee::class);
    }
}
