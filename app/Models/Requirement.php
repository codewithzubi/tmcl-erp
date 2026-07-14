<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    protected $fillable = [
        'requirement_code', 'customer_id', 'product_type', 'product_specifications',
        'quantity', 'unit_of_measure', 'packaging_requirement', 'delivery_location',
        'expected_delivery_date', 'additional_notes',
    ];

    protected $casts = ['expected_delivery_date' => 'date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
