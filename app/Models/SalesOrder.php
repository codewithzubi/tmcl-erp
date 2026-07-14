<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'sales_order_number', 'customer_id', 'linked_proposal_id', 'linked_purchase_order_id',
        'order_date', 'order_value', 'approval_status', 'production_status', 'logistics_status',
        'invoice_status', 'payment_status', 'overall_status',
    ];

    protected $casts = ['order_date' => 'date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'linked_proposal_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(CustomerPurchaseOrder::class, 'linked_purchase_order_id');
    }

    public function purchaseRequisitions()
    {
        return $this->hasMany(PurchaseRequisition::class, 'linked_sales_order_id');
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }
}
