<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlaughterRecord extends Model
{
    protected $fillable = [
        'animal_code', 'lot_id', 'sales_order_number', 'animal_sequence_number',
        'slaughter_date', 'slaughter_operator', 'processing_status', 'remarks',
        'supplier_id', 'customer_id', 'agent', 'doctor', 'meat_checker', 'destination',
        'final_product', 'planned_chiller_id', 'belt_attachment', 'carcass_type',
        'end_slaughter_at', 'rejection_weight', 'final_weight',
    ];

    protected $casts = [
        'slaughter_date' => 'date',
        'end_slaughter_at' => 'datetime',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function plannedChiller()
    {
        return $this->belongsTo(StorageUnit::class, 'planned_chiller_id');
    }

    public function offalRecoveries()
    {
        return $this->hasMany(OffalRecovery::class);
    }

    public function carcassWeightRecord()
    {
        return $this->hasOne(CarcassWeightRecord::class);
    }

    public function veterinaryInspections()
    {
        return $this->hasMany(VeterinaryInspection::class);
    }

    public function meatDeductions()
    {
        return $this->hasMany(MeatDeduction::class);
    }

    public function bonelessRecord()
    {
        return $this->hasOne(BonelessProcessingRecord::class);
    }

    public function botiRecord()
    {
        return $this->hasOne(BotiProcessingRecord::class);
    }
}
