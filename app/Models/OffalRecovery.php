<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffalRecovery extends Model
{
    protected $table = 'offal_recoveries';

    protected $fillable = [
        'recovery_number', 'slaughter_record_id', 'recovery_date', 'recovery_type',
        'measured_weight', 'recorded_by', 'remarks',
    ];

    protected $casts = ['recovery_date' => 'date'];

    public function slaughterRecord()
    {
        return $this->belongsTo(SlaughterRecord::class);
    }
}
