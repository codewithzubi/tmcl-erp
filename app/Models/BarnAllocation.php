<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarnAllocation extends Model
{
    protected $fillable = [
        'allocation_number', 'grn_id', 'barn', 'batch_number', 'livestock_type',
        'number_of_animals_allocated', 'total_weight', 'allocation_date', 'supervisor',
        'remarks', 'allocation_status',
    ];

    protected $casts = ['allocation_date' => 'date'];

    public function grn()
    {
        return $this->belongsTo(Grn::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }
}
