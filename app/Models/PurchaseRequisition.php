<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequisition extends Model
{
    protected $fillable = [
        'pr_number', 'pr_date', 'linked_sales_order_id', 'requesting_department',
        'procurement_officer', 'livestock_type', 'required_quantity', 'estimated_weight_kg',
        'expected_delivery_date', 'priority', 'remarks', 'status',
    ];

    protected $casts = [
        'pr_date' => 'date',
        'expected_delivery_date' => 'date',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'linked_sales_order_id');
    }

    public function quotations()
    {
        return $this->hasMany(SupplierQuotation::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(SupplierPurchaseOrder::class);
    }
}
