<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DutyRoster extends Model
{
    protected $fillable = ['user_id', 'shift_id', 'duty_date', 'department', 'status', 'remarks'];

    protected $casts = ['duty_date' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
