<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalLineItem extends Model
{
    protected $fillable = [
        'proposal_id', 'product', 'description', 'quantity', 'unit', 'unit_price',
        'discount_pct', 'tax_pct', 'packaging_charges', 'logistics_charges',
        'freight_charges', 'insurance_charges', 'other_charges',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
}
