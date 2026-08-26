<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeboningReportEntry extends Model
{
    protected $fillable = [
        'boneless_processing_record_id', 'production_date', 'description', 'no_of_animals',
        'cut_breakdown', 'new_balance_boneless', 'old_balance_boneless_used', 'send_for_other_party',
        'trimming', 'rejected_flank', 'rejected_meat', 'wastage', 'tendon', 'boneless_boti',
        'kitchen_issued', 'bone_issued', 'nalli_issued', 'fat_issued', 'irani_dr_vet_code', 'remarks',
    ];

    protected $casts = [
        'production_date' => 'date',
        'cut_breakdown' => 'array',
    ];

    public function bonelessProcessingRecord()
    {
        return $this->belongsTo(BonelessProcessingRecord::class);
    }
}
