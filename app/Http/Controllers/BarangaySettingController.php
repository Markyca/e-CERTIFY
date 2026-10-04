<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangaySetting;
use App\Models\AuditLog; // Ensure AuditLog is imported
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Ensure Auth is imported

class BarangaySettingController extends Controller
{
    // Display the settings page
    public function edit()
    {
        // Get the single settings record, or create a default one if it doesn't exist yet
        $settings = BarangaySetting::first() ?? BarangaySetting::create([
            'captain_name' => 'HON. JOVENCIO P. EGIPTO',
            'captain_title' => 'Punong Barangay',
            'show_signature' => true,
        ]);

        return view('settings.index', compact('settings'));
    }

    // Update the settings and handle file uploads
    public function update(Request $request)
    {
        $settings = BarangaySetting::first() ?? BarangaySetting::create([
            'captain_name' => 'HON. JOVENCIO P. EGIPTO',
            'captain_title' => 'Punong Barangay',
            'show_signature' => true,
        ]);

        $request->validate([
            'header_lines' => 'required|string|max:600',
            'header_office' => 'required|string|max:255',
            'footer_label' => 'required|string|max:255',
            'footer_address' => 'required|string|max:255',
            'footer_email' => 'nullable|email|max:255',
            'captain_name' => 'required|string|max:255',
            'captain_title' => 'required|string|max:255',
            'lgu_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'brgy_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'captain_signature' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $data = [
            'captain_name' => $request->captain_name,
            'captain_title' => $request->captain_title,
            'show_signature' => $request->has('show_signature'),
            // Global header / footer shared by every certificate
            'header_lines' => implode("\n", array_slice(array_values(array_filter(
                array_map('trim', preg_split('/\r\n|\r|\n/', $request->header_lines)),
                fn ($l) => $l !== ''
            )), 0, 6)),
            'header_office' => trim($request->header_office),
            'footer_label' => trim($request->footer_label),
            'footer_address' => trim($request->footer_address),
            'footer_email' => $request->filled('footer_email') ? trim($request->footer_email) : null,
        ];

        // Handle file uploads safely
        $imageFields = ['lgu_logo', 'brgy_logo', 'qr_code', 'captain_signature'];
        
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old image if it exists
                if ($settings->$field && Storage::disk('public')->exists($settings->$field)) {
                    Storage::disk('public')->delete($settings->$field);
                }
                // Store new image in 'public/settings' folder
                $data[$field] = $request->file($field)->store('settings', 'public');
            }
        }

        $settings->update($data);

        // RECORD AUDIT LOG FOR SETTINGS UPDATE
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'SETTINGS_UPDATED',
            'description' => "Updated global Barangay configuration, signatories, and certificate header/footer.",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('settings.edit')->with('success', 'Barangay global settings updated successfully.');
    }

    // Instant AJAX update for the dashboard digital signature toggle
    public function updateSignatureAjax(Request $request)
    {
        $settings = BarangaySetting::first() ?? BarangaySetting::create([
            'captain_name' => 'HON. JOVENCIO P. EGIPTO',
            'captain_title' => 'Punong Barangay',
            'show_signature' => true,
        ]);

        $settings->show_signature = $request->input('show_signature', 0);
        $settings->save();

        // RECORD AUDIT LOG FOR SIGNATURE TOGGLE
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'SIGNATURE_TOGGLE',
            'description' => "Toggled global digital signature visibility to: " . ($settings->show_signature ? 'Enabled' : 'Disabled'),
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true, 
            'show_signature' => $settings->show_signature
        ]);
    }
}