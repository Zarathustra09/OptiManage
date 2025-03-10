<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Availability extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'day', 'available_from', 'available_to', 'status', 'shift_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
