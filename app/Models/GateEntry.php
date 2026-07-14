<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GateEntry extends Model
{
    protected $fillable = [
        'gate_entry_number', 'entry_date_time', 'entry_type', 'visitor_name', 'supplier_id',
        'driver_name', 'driver_cnic', 'vehicle_registration_number', 'vehicle_type',
        'trailer_number', 'number_of_animals', 'estimated_weight', 'security_officer',
        'purpose_of_visit', 'remarks', 'approval_status',
    ];

    protected $casts = ['entry_date_time' => 'datetime'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function attachments()
    {
        return $this->hasMany(GateEntryAttachment::class);
    }

    public function grns()
    {
        return $this->hasMany(Grn::class);
    }
}
