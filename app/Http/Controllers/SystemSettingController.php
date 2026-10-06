<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    // Singleton settings row — always operates on the first (and only) record.

    public function show()
    {
        return SystemSetting::firstOrCreate([], [
            'company_name' => 'The Organic Meat Company Limited',
            'time_zone' => 'Asia/Karachi',
            'date_format' => 'YYYY-MM-DD',
            'default_currency' => 'PKR',
            'language' => 'English',
            'day_start_hour' => 6,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'time_zone' => ['nullable', 'string', 'max:255'],
            'date_format' => ['nullable', 'string', 'max:255'],
            'default_currency' => ['nullable', 'string', 'max:10'],
            'language' => ['nullable', 'string', 'max:255'],
            'day_start_hour' => ['nullable', 'integer', 'min:0', 'max:23'],
        ]);

        $settings = SystemSetting::firstOrNew([]);
        $settings->fill($data)->save();

        return $settings;
    }
}
