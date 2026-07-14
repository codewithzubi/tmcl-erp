<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['company_name', 'time_zone', 'date_format', 'default_currency', 'language'];
}
