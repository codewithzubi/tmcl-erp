<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $fillable = ['name', 'vendor', 'container_date', 'serial', 'status', 'closed_at'];

    protected $casts = [
        'container_date' => 'date:Y-m-d',
        'serial' => 'integer',
        'closed_at' => 'datetime',
    ];

    public function meatTransferEntries()
    {
        return $this->hasMany(MeatTransferEntry::class);
    }
}
