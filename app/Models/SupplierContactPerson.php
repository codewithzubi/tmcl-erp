<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierContactPerson extends Model
{
    protected $fillable = ['supplier_id', 'name', 'designation', 'email', 'mobile', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
