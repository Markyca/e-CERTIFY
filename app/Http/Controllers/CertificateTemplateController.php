<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangaySetting;

class CertificateTemplateController extends Controller
{
    public function show($type)
    {
        // Fetch global barangay settings (logos, captain name, signature toggle)
        $settings = BarangaySetting::first() ?? BarangaySetting::create([
            'captain_name' => 'HON. JOVENCIO P. EGIPTO',
            'captain_title' => 'Punong Barangay',
            'show_signature' => true,
        ]);

        // Map each type to its own dedicated Blade file inside resources/views/certificates/
        $views = [
            'clearance' => 'certificates.clearance',
            'residency' => 'certificates.residency',
            'indigency' => 'certificates.indigency',
            'jobseeker' => 'certificates.jobseeker',
            'oath' => 'certificates.oath', // Added Oath of Undertaking mapping
        ];

        if (!array_key_exists($type, $views)) {
            abort(404, 'Certificate template not found.');
        }

        return view($views[$type], compact('settings', 'type'));
    }
}