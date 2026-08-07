<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarcassWeightPiece extends Model
{
    protected $fillable = [
        'carcass_weight_record_id', 'piece_name', 'serial_no', 'composite_id',
    ];

    public function carcassWeightRecord()
    {
        return $this->belongsTo(CarcassWeightRecord::class);
    }
}
