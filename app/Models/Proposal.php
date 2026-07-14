<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    protected $fillable = [
        'proposal_number', 'customer_id', 'proposal_date', 'valid_until', 'currency',
        'status', 'version_number', 'prepared_by', 'internal_remarks',
    ];

    protected $casts = [
        'proposal_date' => 'date',
        'valid_until' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lineItems()
    {
        return $this->hasMany(ProposalLineItem::class);
    }
}
