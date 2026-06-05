<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    /**
     * Display a listing of system settings.
     */
    public function index()
    {
        $settings = [
            'school_name' => Setting::get('school_name', 'SMAN X SURABAYA'),
            'school_address' => Setting::get('school_address', 'Jl. Contoh No. 1, Surabaya, Jawa Timur'),
            'school_phone' => Setting::get('school_phone', '(031) 000-0000'),
            'school_logo' => Setting::get('school_logo'),
            'headmaster_name' => Setting::get('headmaster_name', '-'),
            'headmaster_nip' => Setting::get('headmaster_nip', '-'),
        ];

        return view('admin.pengaturan', compact('settings'));
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string',
            'school_phone' => 'nullable|string|max:50',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'headmaster_name' => 'nullable|string|max:255',
            'headmaster_nip' => 'nullable|string|max:100',
        ]);

        // Save normal text settings
        $keys = [
            'school_name' => 'school',
            'school_address' => 'school',
            'school_phone' => 'school',
            'headmaster_name' => 'school',
            'headmaster_nip' => 'school',
        ];

        foreach ($keys as $key => $group) {
            Setting::set($key, $request->input($key), $group);
        }

        // Handle file upload
        if ($request->hasFile('school_logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::get('school_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Store new logo
            $path = $request->file('school_logo')->store('settings/logo', 'public');
            Setting::set('school_logo', $path, 'school');
        }

        return redirect()->route('pengaturan.index')->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}
