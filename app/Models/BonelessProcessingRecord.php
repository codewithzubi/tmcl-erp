<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonelessProcessingRecord extends Model
{
    protected $fillable = [
        'processing_number', 'lot_id', 'slaughter_record_id', 'processing_date',
        'input_weight', 'boneless_weight', 'bone_weight', 'operator', 'status',
    ];

    protected $casts = ['processing_date' => 'date'];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function slaughterRecord()
    {
        return $this->belongsTo(SlaughterRecord::class);
    }
}
