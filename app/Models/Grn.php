<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    protected $table = 'grns';

    protected $fillable = [
        'grn_number', 'supplier_purchase_order_id', 'supplier_id', 'gate_entry_id',
        'receipt_date', 'number_of_animals_received', 'total_weight_received',
        'receiving_officer', 'inspection_status', 'accepted_animals', 'rejected_animals',
        'accepted_weight', 'rejected_weight', 'rejection_reason', 'veterinary_remarks', 'status',
    ];

    protected $casts = ['receipt_date' => 'date'];

    public function purchaseOrder()
    {
        return $this->belongsTo(SupplierPurchaseOrder::class, 'supplier_purchase_order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function gateEntry()
    {
        return $this->belongsTo(GateEntry::class);
    }

    public function livestockInspections()
    {
        return $this->hasMany(LivestockInspection::class);
    }

    public function barnAllocations()
    {
        return $this->hasMany(BarnAllocation::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }
}
