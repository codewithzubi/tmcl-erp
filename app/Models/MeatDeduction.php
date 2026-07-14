<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeatDeduction extends Model
{
    protected $fillable = [
        'slaughter_record_id', 'deduction_type', 'rejected_portion', 'rejected_weight',
        'reason', 'remarks', 'approved_by',
    ];

    public function slaughterRecord()
    {
        return $this->belongsTo(SlaughterRecord::class);
    }
}
