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
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'time_zone' => ['required', 'string', 'max:255'],
            'date_format' => ['required', 'string', 'max:255'],
            'default_currency' => ['required', 'string', 'max:10'],
            'language' => ['required', 'string', 'max:255'],
        ]);

        $settings = SystemSetting::firstOrNew([]);
        $settings->fill($data)->save();

        return $settings;
    }
}
