<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Support\NameFormatter;


class CertificateController extends Controller
{
    // Show the form to select the document type and purpose
    public function create(Resident $resident)
    {
        return view('certificates.create', compact('resident'));
    }

    // Process the request, generate the control number, and save to database
    public function store(Request $request)
    {
        $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'certificate_type' => 'required|string',
            'purpose' => 'required|string|max:255',
            'witness_name' => 'nullable|string|max:255',
            'witness_title' => 'nullable|string|max:255',
        ]);

        // A control number that is guaranteed not to repeat
        do {
            $controlNumber = 'BRGY-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (Certificate::where('control_number', $controlNumber)->exists());

        $settings = \App\Models\BarangaySetting::ensure();
        $duty = $settings->duty();                       // set when the Punong Barangay is away
        $isJobSeeker = $request->certificate_type === 'First Time Job Seeker';
        $defaultWitness = $settings->witness();

        // 1. Create the certificate record (who signed / witnessed is saved with it)
        $certificate = Certificate::create([
            'resident_id' => $request->resident_id,
            'certificate_type' => $request->certificate_type,
            'purpose' => $request->purpose,
            'control_number' => $controlNumber,
            'issued_by' => Auth::id(),
            'duty_kagawad_name' => $duty['name'] ?? null,
            'duty_kagawad_title' => $duty['title'] ?? null,
            'witness_name' => $isJobSeeker ? (trim((string) $request->witness_name) ?: $defaultWitness['name']) : null,
            'witness_title' => $isJobSeeker ? (trim((string) $request->witness_title) ?: $defaultWitness['title']) : null,
        ]);

        // 3. RECORD THE AUDIT LOG HERE
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'CERTIFICATE_ISSUED',
            'description' => "Issued a {$certificate->certificate_type} (Control No: {$controlNumber}) for resident {$certificate->resident->first_name} {$certificate->resident->last_name}" . ($duty ? " (signed by duty kagawad {$duty['name']})." : '.'),
            'ip_address' => request()->ip(),
        ]);

        // 4. Redirect to the print preview page
        return redirect()->route('certificates.show', $certificate->id);
    }

    public function show(Certificate $certificate)
    {
        $resident = $certificate->resident;
        $settings = \App\Models\BarangaySetting::first() ?? \App\Models\BarangaySetting::create([
            'captain_name' => 'HON. JOVENCIO P. EGIPTO',
            'captain_title' => 'Punong Barangay',
            'show_signature' => true,
        ]);

        // Names: MR. / MS. / MRS. (married, widowed), middle initial, name extension (Jr., Sr., III)
        $formalFullName = NameFormatter::formal($resident);
        $standardFullName = NameFormatter::standard($resident);

        // Every date and the age are those of the day the certificate was ISSUED (a reprint must not change them)
        $issued = $certificate->created_at ?? now();
        $age = \Carbon\Carbon::parse($resident->birth_date)->diff($issued)->y;

        // Values available to the {shortcodes} used in editable certificate bodies
        $vars = [
            'name'          => $formalFullName,
            'standard_name' => $standardFullName,
            'age'           => $age,
            'gender'        => mb_strtolower(trim((string) $resident->gender), 'UTF-8'),
            'civil_status'  => mb_strtolower(trim((string) $resident->civil_status), 'UTF-8'),
            'purok'         => $resident->purok,
            'purpose'       => NameFormatter::purposeText($certificate->purpose),
            'day'           => $issued->format('jS'),
            'month_year'    => $issued->format('F Y'),
            'his_her'       => NameFormatter::hisHer($resident->gender),
            'he_she'        => NameFormatter::heShe($resident->gender),
            'legal_age'     => NameFormatter::legalAge($age),
        ];

        // Date printed beside the signatories
        $issuedDate = $issued->format('F j, Y');

        // Duty kagawad signs in place of the Punong Barangay (saved with the certificate at issue time)
        $dutyKagawad = $certificate->duty_kagawad_name
            ? ['name' => $certificate->duty_kagawad_name, 'title' => $certificate->duty_kagawad_title ?: \App\Models\BarangaySetting::DEFAULT_DUTY_TITLE]
            : null;

        // DETOUR: If it is a First Time Job Seeker, render the 2-page set
        if ($certificate->certificate_type == 'First Time Job Seeker') {
            // The witness saved with the certificate at issue time (older certificates: the default witness)
            $defaultWitness = $settings->witness();
            $witnessName = $certificate->witness_name ?: $defaultWitness['name'];
            $witnessTitle = $certificate->witness_title ?: $defaultWitness['title'];

            $certTemplate = CertificateTemplate::forType('jobseeker');
            $oathTemplate = CertificateTemplate::forType('oath');
            $certBodyHtml = $certTemplate->renderBody($vars);
            $oathBodyHtml = $oathTemplate->renderBody($vars);

            return view('certificates.print-jobseeker', compact(
                'certificate',
                'resident',
                'settings',
                'witnessName',
                'witnessTitle',
                'issuedDate',
                'dutyKagawad',
                'formalFullName',
                'standardFullName',
                'certTemplate',
                'oathTemplate',
                'certBodyHtml',
                'oathBodyHtml'
            ));
        }

        // Regular certificates: clearance / indigency / residency (unknown types fall back to clearance)
        $templateKey = CertificateTemplate::CERTIFICATE_TYPE_KEYS[$certificate->certificate_type] ?? 'clearance';
        $template = CertificateTemplate::forType($templateKey);
        $certificateTitle = $template->title;
        $salutation = $template->salutation;
        $bodyHtml = $template->renderBody($vars);

        return view('certificates.print', compact(
            'certificate',
            'resident',
            'settings',
            'certificateTitle',
            'salutation',
            'issuedDate',
            'dutyKagawad',
            'bodyHtml'
        ));
    }

    public function history(Request $request)
    {
        $query = Certificate::with(['resident']);

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('type')) {
            $query->where('certificate_type', $request->type);
        }

        // Only need standard pagination now!
        $certificates = $query->latest()->paginate(10)->appends($request->query());

        $filterText = "All-Time Document History Report";
        if ($request->filled('date')) {
            $filterText = "Report for Date: " . date('F d, Y', strtotime($request->date));
        } elseif ($request->filled('month') && $request->filled('year')) {
            $monthName = date('F', mktime(0, 0, 0, $request->month, 1));
            $filterText = "Report for {$monthName} {$request->year}";
        } elseif ($request->filled('year')) {
            $filterText = "Report for Year {$request->year}";
        } elseif ($request->filled('month')) {
            $monthName = date('F', mktime(0, 0, 0, $request->month, 1));
            $filterText = "Report for the month of {$monthName}";
        }

        if ($request->filled('type')) {
            $filterText .= " — Type: " . $request->type;
        }

        return view('certificates.history', compact('certificates', 'filterText'));
    }

    public function printHistory(Request $request)
    {
        $query = Certificate::with(['resident']);

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }
        if ($request->filled('type')) {
            $query->where('certificate_type', $request->type);
        }

        // Get ALL matching records without pagination
        $certificates = $query->latest()->get();

        // Build filter text
        $filterText = "All-Time Document History Report";
        if ($request->filled('month') && $request->filled('year')) {
            $monthName = date('F', mktime(0, 0, 0, $request->month, 1));
            $filterText = "Report for {$monthName} {$request->year}";
        } elseif ($request->filled('year')) {
            $filterText = "Report for Year {$request->year}";
        } elseif ($request->filled('month')) {
            $monthName = date('F', mktime(0, 0, 0, $request->month, 1));
            $filterText = "Report for the month of {$monthName}";
        }

        return view('certificates.print-history', compact('certificates', 'filterText'));
    }

}