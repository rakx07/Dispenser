<?php

namespace App\Http\Controllers; // ✅ Add this

use App\Models\CredentialDisplaySetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CredentialDisplayController extends Controller
{
    public function index()
    {
        $settings = CredentialDisplaySetting::all()->keyBy('section');
        return view('controls.index', compact('settings'));
    }

    public function toggle(Request $request)
    {
        $setting = CredentialDisplaySetting::where('section', $request->section)->firstOrFail();
        $setting->is_enabled = !$setting->is_enabled;
        $setting->save();

        return response()->json(['success' => true, 'status' => $setting->is_enabled]);
    }
}
