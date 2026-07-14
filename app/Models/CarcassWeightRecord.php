<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarcassWeightRecord extends Model
{
    protected $fillable = [
        'slaughter_record_id', 'hanging_weight', 'weight_date_time', 'scale_id',
        'left_hind_quarter', 'right_hind_quarter', 'left_fore_quarter', 'right_fore_quarter',
        'manual_override', 'supervisor_approval', 'final_carcass_weight',
        'gender', 'specie', 'age', 'teeth', 'hook_weight', 'photo_path', 'locked',
    ];

    protected $casts = [
        'weight_date_time' => 'datetime',
        'manual_override' => 'boolean',
        'locked' => 'boolean',
    ];

    public function slaughterRecord()
    {
        return $this->belongsTo(SlaughterRecord::class);
    }
}
