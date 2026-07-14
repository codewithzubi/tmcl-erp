<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPurchaseOrder extends Model
{
    protected $fillable = [
        'purchase_order_number', 'customer_id', 'linked_proposal_id', 'po_date',
        'delivery_date', 'status', 'internal_remarks',
    ];

    protected $casts = [
        'po_date' => 'date',
        'delivery_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'linked_proposal_id');
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'linked_purchase_order_id');
    }
}
