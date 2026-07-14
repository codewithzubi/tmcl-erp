<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestockSupplyRecord extends Model
{
    protected $fillable = [
        'supplier_id', 'grn_number', 'livestock_type', 'number_of_animals',
        'total_weight_kg', 'receipt_date', 'status',
    ];

    protected $casts = ['receipt_date' => 'date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
