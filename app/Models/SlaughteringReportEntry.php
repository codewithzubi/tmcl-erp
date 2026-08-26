<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlaughteringReportEntry extends Model
{
    protected $fillable = [
        'slaughter_record_id', 'dual_pcs', 'quarter_pcs', 'total_gross_weight', 'live_weight', 'freight',
    ];

    public function slaughterRecord()
    {
        return $this->belongsTo(SlaughterRecord::class);
    }
}
