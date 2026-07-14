<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDiscussionNote extends Model
{
    protected $fillable = ['customer_id', 'author', 'note'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
