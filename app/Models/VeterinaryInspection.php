<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VeterinaryInspection extends Model
{
    protected $fillable = [
        'inspection_number', 'slaughter_record_id', 'doctor', 'inspection_date',
        'inspection_result', 'disease_observation', 'remarks',
    ];

    protected $casts = ['inspection_date' => 'date'];

    public function slaughterRecord()
    {
        return $this->belongsTo(SlaughterRecord::class);
    }
}
