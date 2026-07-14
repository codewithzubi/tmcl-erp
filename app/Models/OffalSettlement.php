<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffalSettlement extends Model
{
    protected $fillable = [
        'supplier_id', 'lot_id', 'by_product_type', 'total_weight', 'disposal_method',
        'purchase_rate', 'purchase_amount', 'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }
}
