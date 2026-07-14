<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPurchaseOrder extends Model
{
    protected $fillable = [
        'purchase_order_number', 'purchase_requisition_id', 'supplier_id', 'quotation_id',
        'po_date', 'delivery_date', 'livestock_type', 'quantity', 'estimated_weight_kg',
        'unit_rate', 'total_amount', 'terms_and_conditions', 'supplier_approval_status',
        'purchase_order_status',
    ];

    protected $casts = [
        'po_date' => 'date',
        'delivery_date' => 'date',
    ];

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function quotation()
    {
        return $this->belongsTo(SupplierQuotation::class, 'quotation_id');
    }

    public function grns()
    {
        return $this->hasMany(Grn::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }
}
