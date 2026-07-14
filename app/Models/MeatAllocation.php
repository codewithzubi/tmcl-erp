<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeatAllocation extends Model
{
    protected $fillable = [
        'allocation_number', 'customer_id', 'sales_order_id', 'lot_id', 'product_type',
        'quantity', 'destination_department', 'allocation_date', 'status',
    ];

    protected $casts = ['allocation_date' => 'date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }
}
