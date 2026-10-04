<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type',
        'title',
        'salutation',
        'body_content',
        'signatory_name',
        'signatory_title',
        'contact_email',
        'lgu_logo',
        'brgy_logo',
        'captain_signature',
    ];

    /** Shortcodes available in every body. */
    public const SHORTCODES = [
        'name'          => 'Full name (formal, e.g. MR. JUAN D. CRUZ)',
        'standard_name' => 'Full name (no MR./MS.)',
        'age'           => 'Age',
        'gender'        => 'Gender',
        'civil_status'  => 'Civil status',
        'purok'         => 'Purok',
        'purpose'       => 'Purpose',
        'day'           => 'Day issued (e.g. 2nd)',
        'month_year'    => 'Month and year issued',
        'barangay'      => 'Barangay name',
        'municipality'  => 'Municipality',
        'province'      => 'Province',
    ];

    /** Maps a template key to the label used in the UI. */
    public const TYPES = [
        'clearance' => 'Barangay Clearance',
        'residency' => 'Certificate of Residency',
        'indigency' => 'Certificate of Indigency',
        'jobseeker' => 'First Time Job Seeker',
        'oath'      => 'Oath of Undertaking',
    ];

    /** Maps the stored certificate_type to its template key. */
    public const CERTIFICATE_TYPE_KEYS = [
        'Barangay Clearance'       => 'clearance',
        'Certificate of Residency' => 'residency',
        'Certificate of Indigency' => 'indigency',
        'First Time Job Seeker'    => 'jobseeker',
    ];

    /**
     * Factory defaults. Bodies are plain text: blank line = new paragraph,
     * **bold**, {shortcodes}, and numbered lines ("1. ...") become a list.
     */
    public static function defaults(string $type): ?array
    {
        $issued = 'Issued this {day} day of {month_year} at Barangay {barangay}, {municipality}, {province}.';

        return match ($type) {
            'clearance' => [
                'title' => 'BARANGAY CLEARANCE',
                'salutation' => 'TO WHOM IT MAY CONCERN:',
                'body_content' => "This is to certify that **{name}**, {age} years of age, {gender}, {civil_status}, is a bona fide resident of Barangay {barangay}, {municipality}, {province}.\n\n"
                    . "Based on our records, the above-named individual is a law-abiding citizen with no pending criminal case or derogatory record in this community.\n\n"
                    . "This certification is issued upon the request of the interested party for whatever legal purpose it may serve.\n\n"
                    . $issued,
            ],
            'residency' => [
                'title' => 'CERTIFICATE OF RESIDENCY',
                'salutation' => 'TO WHOM IT MAY CONCERN:',
                'body_content' => "This is to certify that **{name}**, of legal age, {gender}, {civil_status}, and a Filipino Citizen. According to the records in this office, the above-named individual is a resident of Purok {purok}, {barangay}, {municipality}, {province}.\n\n"
                    . "This is issued upon the request of the interested party who needs the foregoing certification for whatever legal purpose it may serve.\n\n"
                    . $issued,
            ],
            'indigency' => [
                'title' => 'CERTIFICATE OF INDIGENCY',
                'salutation' => 'TO WHOM IT MAY CONCERN:',
                'body_content' => "This is to certify that **{name}**, {age} years of age, {gender}, {civil_status}, and a Filipino citizen, is a bona fide resident of Barangay {barangay}, {municipality}, {province}, and belongs to an **indigent family** in this barangay.\n\n"
                    . "This certification is issued upon the request of the interested party, who needs the foregoing instrument for {purpose} and for whatever legal purpose it may serve.\n\n"
                    . $issued,
            ],
            'jobseeker' => [
                'title' => 'BARANGAY CERTIFICATION',
                'salutation' => 'TO WHOM IT MAY CONCERN:',
                'body_content' => "THIS IS TO CERTIFY that **{name}**, a resident of Barangay {barangay}, {municipality}, {province}, is a qualified applicant under RA 11261 known as the First-Time Job Seekers Act of 2019.\n\n"
                    . "I further certify that the bearer was informed of her rights, including the duties and responsibilities accorded by RA 11261, through the Oath of Undertaking she has signed and executed in the presence of our Barangay Official.\n\n"
                    . 'Signed this {day} day of {month_year} at Barangay {barangay}, {municipality}, {province}.',
            ],
            'oath' => [
                'title' => 'OATH OF UNDERTAKING',
                'salutation' => '',
                'body_content' => "**I, {standard_name}**, {age} years of age, resident of Barangay {barangay}, {municipality}, {province}, availing the benefit of Republic Act **11261**, otherwise known as the **First Time Jobseekers Act of 2019**, agree and undertake to abide and be bound by the following:\n\n"
                    . "1. That is the first time that I will actively take a job and therefore request that the Barangay Certification be issued in my favor to avail the benefit of the law;\n"
                    . "2. That I am aware that the benefit and privilege/s under the said law shall be valid only for one (1) year from the date that the Barangay Certification is issued;\n"
                    . "3. That I can avail myself of the benefits of the law only once;\n"
                    . "4. That I understand that my personal information shall be included in the Roster/List of the First Time Jobseekers and not be used for any unlawful purpose;\n"
                    . "5. That I will inform and/or report to the Barangay personally, through text or other options, that I am not a beneficiary of the Job Start Program under RA No. 10869 and other laws that give similar exemptions for the documents and other transactions exempted under RA No. 11261;\n"
                    . "6. That if issued the requested Certification, I will not use the same in any fraud, nor falsify nor help and/or assist in the fabrication of the said certification;\n"
                    . "7. That this undertaking is made solely for the purpose of obtaining a barangay Certification consistent with the objective of RA No. 11261, and/or not for any other purpose;\n"
                    . "8. That I consent to the use of my personal information pursuant to the Data Privacy Act and other applicable laws, rules, and regulations; and\n"
                    . "9. That I consent to the use of my personal information pursuant to the Data Privacy Act and other applicable laws, rules, and regulations.\n\n"
                    . 'Signed this {day} day of {month_year}, at Barangay {barangay}, {municipality}, {province}.',
            ],
            default => null,
        };
    }

    /** Saved template if present, otherwise an unsaved instance holding the defaults. */
    public static function forType(string $type): self
    {
        $saved = static::where('document_type', $type)->first();
        if ($saved) {
            return $saved;
        }

        $defaults = static::defaults($type) ?? abort(404, 'Certificate template not found.');

        return new static($defaults + ['document_type' => $type]);
    }

    /**
     * Turn the stored plain-text body into safe HTML, replacing {shortcodes}.
     * Output uses <p> and <ul class="tpl-list"> so each view can style it.
     */
    public function renderBody(array $vars = []): string
    {
        // Barangay / municipality / province always come from the Barangay page
        $vars = $vars + BarangaySetting::current()->identity();

        $text = str_replace(["\r\n", "\r"], "\n", (string) $this->body_content);
        $html = '';

        foreach (preg_split('/\n{2,}/', trim($text)) as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }

            $lines = explode("\n", $block);
            $isList = count(array_filter($lines, fn ($l) => preg_match('/^\s*\d+\.\s/', $l))) === count($lines);

            if ($isList) {
                $html .= '<ul class="tpl-list">';
                foreach ($lines as $line) {
                    preg_match('/^\s*(\d+\.)\s*(.*)$/', $line, $m);
                    $html .= '<li><strong>' . e($m[1]) . '</strong> ' . static::inline($m[2], $vars) . '</li>';
                }
                $html .= '</ul>';
            } else {
                $html .= '<p>' . static::inline(implode(' ', array_map('trim', $lines)), $vars) . '</p>';
            }
        }

        return $html;
    }

    public function renderInline(string $text, array $vars = []): string
    {
        return static::inline($text, $vars);
    }

    protected static function inline(string $text, array $vars): string
    {
        $text = e($text);

        $text = preg_replace_callback('/\{(\w+)\}/', function ($m) use ($vars) {
            return array_key_exists($m[1], $vars) ? e((string) $vars[$m[1]]) : $m[0];
        }, $text);

        return preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
    }
}
