<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carton extends Model
{
    protected $fillable = [
        'carton_number', 'lot_id', 'number_of_packets', 'carton_weight_kg',
        'packaging_material', 'barcode', 'label_printed', 'status',
    ];

    protected $casts = ['label_printed' => 'boolean'];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }
}
