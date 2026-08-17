<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeatTransferEntry extends Model
{
    protected $fillable = [
        'slaughter_record_id', 'chiller_name', 'chiller_out_time',
        'transaction_type', 'transfer_department', 'quantity', 'tag_allocations',
    ];

    protected $casts = [
        'chiller_out_time' => 'datetime',
        'tag_allocations' => 'array',
    ];

    public function slaughterRecord()
    {
        return $this->belongsTo(SlaughterRecord::class);
    }
}
