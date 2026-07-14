<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = [
        'lot_code', 'lot_name', 'sales_order_id', 'customer_id', 'supplier_purchase_order_id',
        'supplier_id', 'grn_id', 'barn_allocation_id', 'batch_number', 'livestock_type',
        'number_of_animals', 'total_live_weight', 'allocation_date', 'created_by', 'status',
        'remarks', 'supplier_committed_weight', 'hold_reason', 'required_remaining_weight',
        'additional_animals_required', 'released_by', 'release_date',
    ];

    protected $casts = [
        'allocation_date' => 'date',
        'release_date' => 'date',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(SupplierPurchaseOrder::class, 'supplier_purchase_order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function grn()
    {
        return $this->belongsTo(Grn::class);
    }

    public function barnAllocation()
    {
        return $this->belongsTo(BarnAllocation::class);
    }

    public function slaughterRecords()
    {
        return $this->hasMany(SlaughterRecord::class);
    }

    public function settlements()
    {
        return $this->hasMany(SupplierSettlement::class);
    }

    public function offalSettlements()
    {
        return $this->hasMany(OffalSettlement::class);
    }

    public function meatAllocations()
    {
        return $this->hasMany(MeatAllocation::class);
    }

    public function storageSessions()
    {
        return $this->hasMany(StorageSession::class);
    }

    public function bonelessRecords()
    {
        return $this->hasMany(BonelessProcessingRecord::class);
    }

    public function botiRecords()
    {
        return $this->hasMany(BotiProcessingRecord::class);
    }

    public function packets()
    {
        return $this->hasMany(Packet::class);
    }

    public function cartons()
    {
        return $this->hasMany(Carton::class);
    }

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }
}
