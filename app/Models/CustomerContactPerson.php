<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerContactPerson extends Model
{
    protected $fillable = ['customer_id', 'name', 'designation', 'email', 'mobile', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
