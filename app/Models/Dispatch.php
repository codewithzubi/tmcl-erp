<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $fillable = [
        'dispatch_number', 'shipment_id', 'lot_id', 'carton_id', 'quantity',
        'total_weight_kg', 'dispatch_time', 'dispatch_officer', 'status',
        'ph_level', 'cloth_check', 'temperature', 'label_check',
    ];

    protected $casts = ['dispatch_time' => 'datetime'];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function carton()
    {
        return $this->belongsTo(Carton::class);
    }
}
