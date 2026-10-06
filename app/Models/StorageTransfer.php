<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageTransfer extends Model
{
    protected $fillable = [
        'slaughter_record_id', 'from_type', 'from_name', 'to_type', 'to_name',
        'supplier_id', 'tag_ids', 'weight', 'comments', 'close_time',
        'transferred_by', 'transferred_at',
    ];

    protected $casts = [
        'tag_ids' => 'array',
        'weight' => 'decimal:2',
        'close_time' => 'datetime',
        'transferred_at' => 'datetime',
    ];

    public function slaughterRecord()
    {
        return $this->belongsTo(SlaughterRecord::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
