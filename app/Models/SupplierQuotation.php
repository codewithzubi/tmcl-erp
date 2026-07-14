<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierQuotation extends Model
{
    protected $fillable = [
        'quotation_number', 'purchase_requisition_id', 'supplier_id', 'quotation_date',
        'price_per_kg', 'number_of_animals', 'total_weight_kg', 'delivery_charges',
        'payment_terms', 'delivery_schedule', 'status',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'delivery_schedule' => 'date',
    ];

    public function purchaseRequisition()
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->hasOne(SupplierPurchaseOrder::class, 'quotation_id');
    }
}
