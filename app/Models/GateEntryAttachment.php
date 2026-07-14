<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GateEntryAttachment extends Model
{
    protected $fillable = ['gate_entry_id', 'slot', 'file_name', 'file_type', 'size_kb'];

    public function gateEntry()
    {
        return $this->belongsTo(GateEntry::class);
    }
}
