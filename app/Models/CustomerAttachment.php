<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAttachment extends Model
{
    protected $fillable = ['customer_id', 'file_name', 'file_type', 'uploaded_by', 'size_kb'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
