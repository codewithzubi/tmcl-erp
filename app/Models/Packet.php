<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packet extends Model
{
    protected $fillable = [
        'packet_number', 'lot_id', 'product_type', 'packet_size_kg', 'number_of_packets',
        'weight_per_packet_kg', 'packaging_material', 'packed_by', 'packing_date',
    ];

    protected $casts = ['packing_date' => 'date'];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }
}
