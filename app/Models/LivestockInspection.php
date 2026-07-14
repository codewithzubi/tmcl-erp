<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestockInspection extends Model
{
    protected $fillable = [
        'inspection_number', 'grn_id', 'veterinary_officer', 'inspection_date',
        'animal_health_status', 'disease_symptoms', 'physical_condition',
        'body_weight_verification', 'temperature', 'quarantine_required',
        'inspection_remarks', 'final_decision',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'quarantine_required' => 'boolean',
    ];

    public function grn()
    {
        return $this->belongsTo(Grn::class);
    }
}
