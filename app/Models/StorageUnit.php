<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageUnit extends Model
{
    protected $fillable = [
        'unit_code', 'name', 'type', 'capacity_kg', 'occupied_kg', 'min_temp',
        'max_temp', 'target_temp', 'status',
    ];

    public function sessions()
    {
        return $this->hasMany(StorageSession::class);
    }
}
