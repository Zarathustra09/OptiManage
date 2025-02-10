<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Defect extends Model
{
    use HasFactory;

    protected $fillable = ['inventory_id', 'quantity', 'reason'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
