<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadingReportEntry extends Model
{
    protected $fillable = [
        'export_entry_id', 'description', 'total_pcs', 'hot_weight', 'basket_crtn',
        'vehicle_no', 'container_no', 'seal_no', 'gate_pass_no',
        'chilling_start_time', 'chilling_end_time', 'indent_no',
        'offload_date_time', 'offload_total_pcs', 'offload_total_weight',
    ];

    public function exportEntry()
    {
        return $this->belongsTo(ExportEntry::class);
    }
}
