<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageSession extends Model
{
    protected $fillable = [
        'session_number', 'storage_unit_id', 'lot_id', 'product_weight',
        'time_in', 'time_out', 'status',
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
    ];

    public function storageUnit()
    {
        return $this->belongsTo(StorageUnit::class);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }
}
