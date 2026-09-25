<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraMaterial extends Model
{
    protected $fillable = ['title', 'cost'];

    protected $casts = [
        'cost' => 'decimal:2',
    ];
}
