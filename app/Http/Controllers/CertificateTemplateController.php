<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BarangaySetting;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateTemplateController extends Controller
{
    /** Placeholder values used for the layout preview and editor. */
    public const SAMPLE_VARS = [
        'name'          => '[RESIDENT NAME]',
        'standard_name' => '[APPLICANT NAME]',
        'age'           => '[AGE]',
        'gender'        => '[GENDER]',
        'civil_status'  => '[CIVIL STATUS]',
        'purok'         => '[PUROK]',
        'purpose'       => '[PURPOSE]',
        'day'           => '[DAY]',
        'month_year'    => '[MONTH] [YEAR]',
    ];

    protected function settings(): BarangaySetting
    {
        return BarangaySetting::first() ?? BarangaySetting::create([
            'captain_name' => 'HON. JOVENCIO P. EGIPTO',
            'captain_title' => 'Punong Barangay',
            'show_signature' => true,
        ]);
    }

    protected function guardType(string $type): void
    {
        abort_unless(array_key_exists($type, CertificateTemplate::TYPES), 404, 'Certificate template not found.');
    }

    /** Layout preview of a certificate (header/footer come from global settings). */
    public function show($type)
    {
        $this->guardType($type);

        $settings = $this->settings();
        $template = CertificateTemplate::forType($type);
        $bodyHtml = $template->renderBody(self::SAMPLE_VARS);

        return view('certificates.' . $type, compact('settings', 'type', 'template', 'bodyHtml'));
    }

    /** Body editor for one certificate. */
    public function edit($type)
    {
        $this->guardType($type);

        $settings = $this->settings();
        $template = CertificateTemplate::forType($type);
        $defaults = CertificateTemplate::defaults($type);
        $label = CertificateTemplate::TYPES[$type];
        $shortcodes = CertificateTemplate::SHORTCODES;
        $isCustom = $template->exists;

        return view('certificates.templates.edit', compact('settings', 'type', 'template', 'defaults', 'label', 'shortcodes', 'isCustom'));
    }

    public function update(Request $request, $type)
    {
        $this->guardType($type);

        $rules = [
            'title' => 'required|string|max:120',
            'salutation' => 'nullable|string|max:255',
            'body_content' => 'required|string|max:8000',
        ];

        // The First Time Job Seeker page also holds the default "Witnessed by"
        if ($type === 'jobseeker') {
            $rules['witness_name'] = 'required|string|max:255';
            $rules['witness_title'] = 'required|string|max:255';
        }

        $data = $request->validate($rules);

        $settings = $this->settings();

        if ($type === 'jobseeker') {
            $settings->update([
                'witness_name' => trim($data['witness_name']),
                'witness_title' => trim($data['witness_title']),
            ]);
        }

        CertificateTemplate::updateOrCreate(
            ['document_type' => $type],
            [
                'title' => $data['title'],
                'salutation' => $data['salutation'] ?? '',
                'body_content' => $data['body_content'],
                'signatory_name' => $settings->captain_name,
                'signatory_title' => $settings->captain_title,
            ]
        );

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'TEMPLATE_UPDATED',
            'description' => 'Edited the body of the ' . CertificateTemplate::TYPES[$type] . ' template' . ($type === 'jobseeker' ? ' and its default witness.' : '.'),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('templates.body', $type)->with('success', 'Certificate body saved.');
    }

    /** Remove the customised body and go back to the factory default. */
    public function reset(Request $request, $type)
    {
        $this->guardType($type);

        CertificateTemplate::where('document_type', $type)->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'TEMPLATE_RESET',
            'description' => 'Restored the default body of the ' . CertificateTemplate::TYPES[$type] . ' template.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('templates.body', $type)->with('success', 'Default body restored.');
    }
}
