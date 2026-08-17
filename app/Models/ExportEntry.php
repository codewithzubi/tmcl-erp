<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportEntry extends Model
{
    protected $fillable = [
        'slaughter_record_id', 'chiller_name', 'chiller_out_time', 'export_date_time',
        'export_quantity', 'destination_country', 'destination_consignee', 'customer_buyer',
        'forwarder_name', 'export_reference', 'remarks', 'export_mode', 'mode_details',
    ];

    protected $casts = [
        'chiller_out_time' => 'datetime',
        'export_date_time' => 'datetime',
        'mode_details' => 'array',
    ];

    public function slaughterRecord()
    {
        return $this->belongsTo(SlaughterRecord::class);
    }
}
