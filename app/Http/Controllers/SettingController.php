<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Models\Setting;

class SettingController extends Controller
{
    private const KEYS = ['restaurant_name', 'address', 'phone', 'vat_rate', 'receipt_footer'];

    public function edit()
    {
        $values = [];
        foreach (self::KEYS as $key) {
            $values[$key] = Setting::get($key, '');
        }

        return view('settings.index', compact('values'));
    }

    public function update(SettingRequest $request)
    {
        foreach (self::KEYS as $key) {
            Setting::put($key, (string) ($request->validated($key) ?? ''));
        }

        return redirect()->route('settings.edit')->with('success', 'Settings saved.');
    }
}
