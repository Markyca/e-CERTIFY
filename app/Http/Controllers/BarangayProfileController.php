<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BarangaySetting;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Admin-only: the barangay's name, municipality and province.
 * Saving updates every place these were previously hard-coded.
 */
class BarangayProfileController extends Controller
{
    public function edit()
    {
        $settings = BarangaySetting::ensure();
        $identity = $settings->identity();

        return view('barangay.edit', compact('settings', 'identity'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'barangay_name' => 'required|string|max:100',
            'municipality'  => 'required|string|max:100',
            'province'      => 'required|string|max:100',
        ]);

        $settings = BarangaySetting::ensure();
        $old = $settings->identity();

        // Tidy input: collapse spaces, drop a typed-in "Barangay"/"Municipality of"/"Province of" prefix
        $clean = fn (string $v, string $prefix) => trim(preg_replace('/^(' . $prefix . ')\s+/iu', '', preg_replace('/\s+/u', ' ', trim($v))));
        $new = [
            'barangay'     => $clean($data['barangay_name'], 'barangay|brgy\.?'),
            'municipality' => $clean($data['municipality'], 'municipality of|town of'),
            'province'     => $clean($data['province'], 'province of'),
        ];

        if (in_array('', $new, true)) {
            return back()->withInput()->withErrors(['barangay_name' => 'Please enter the full name.']);
        }

        DB::transaction(function () use ($settings, $old, $new) {
            $swap = fn (?string $text) => BarangaySetting::swapIdentity($text, $old, $new);

            // Header / footer text that already contains the old names
            $settings->forceFill([
                'barangay_name' => $new['barangay'],
                'municipality'  => $new['municipality'],
                'province'      => $new['province'],
                'header_lines'  => $swap($settings->header_lines),
                'header_office' => $swap($settings->header_office),
                'footer_address' => $swap($settings->footer_address),
            ])->save();

            // Certificate bodies that were customised before and still contain the old names
            CertificateTemplate::all()->each(function ($template) use ($swap) {
                $updated = $swap($template->body_content);
                if ($updated !== $template->body_content) {
                    $template->update(['body_content' => $updated]);
                }
            });
        });

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'BARANGAY_UPDATED',
            'description' => sprintf(
                'Changed barangay details: %s, %s, %s  →  %s, %s, %s.',
                $old['barangay'], $old['municipality'], $old['province'],
                $new['barangay'], $new['municipality'], $new['province']
            ),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('barangay.edit')->with('success', 'Barangay details saved and applied everywhere.');
    }
}
