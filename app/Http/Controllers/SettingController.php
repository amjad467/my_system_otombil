<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'org_name'        => Setting::get('org_name', 'سیستەمی بەڕێوەبردنی ئۆتۆمبێل'),
            'attention_hours' => Setting::get('attention_hours', '2'),
            'warning_hours'   => Setting::get('warning_hours', '4'),
            'dark_mode'       => Setting::get('dark_mode', '0'),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'org_name'        => 'required|string|max:150',
            'attention_hours' => 'required|numeric|min:0.5|max:24',
            'warning_hours'   => 'required|numeric|gt:attention_hours|max:72',
            'dark_mode'       => 'nullable|boolean',
        ]);

        Setting::set('org_name', $data['org_name'], 'general');
        Setting::set('attention_hours', (string)$data['attention_hours'], 'thresholds');
        Setting::set('warning_hours', (string)$data['warning_hours'], 'thresholds');
        Setting::set('dark_mode', $request->has('dark_mode') ? '1' : '0', 'appearance');

        AuditLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'settings_updated',
            'model_type'   => Setting::class,
            'performed_at' => now(),
            'details'      => $data,
        ]);

        return back()->with('success', 'ڕێکخستنەکان بە سەرکەوتوویی نوێکرانەوە.');
    }
}
