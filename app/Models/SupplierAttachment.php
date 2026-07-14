<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierAttachment extends Model
{
    protected $fillable = ['supplier_id', 'file_name', 'file_type', 'uploaded_by', 'size_kb'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
