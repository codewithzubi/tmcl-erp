<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierSettlement extends Model
{
    protected $fillable = [
        'settlement_number', 'supplier_id', 'lot_id', 'agreed_rate_per_kg',
        'approved_meat_weight', 'total_settlement_amount', 'payment_method',
        'payment_date', 'settlement_status', 'approved_by', 'remarks',
    ];

    protected $casts = ['payment_date' => 'date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }
}
