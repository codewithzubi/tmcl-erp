<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldDefinition extends Model
{
    protected $fillable = [
        'module', 'field_key', 'label', 'field_type', 'options', 'required', 'sort_order', 'status',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];

    public function values()
    {
        return $this->hasMany(CustomFieldValue::class);
    }
}
