<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; 


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

        $controlNumber = 'BRGY-' . date('Y') . '-' . strtoupper(Str::random(6));

        // 1. Create the certificate record
        $certificate = Certificate::create([
            'resident_id' => $request->resident_id,
            'certificate_type' => $request->certificate_type,
            'purpose' => $request->purpose,
            'control_number' => $controlNumber,
            'issued_by' => Auth::id(),
        ]);

        // 2. Store witness info in session if it's a job seeker
        if ($request->certificate_type === 'First Time Job Seeker') {
            session([
                'witness_name' => $request->witness_name,
                'witness_title' => $request->witness_title,
            ]);
        }

        // 3. RECORD THE AUDIT LOG HERE
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'CERTIFICATE_ISSUED',
            'description' => "Issued a {$certificate->certificate_type} (Control No: {$controlNumber}) for resident {$certificate->resident->first_name} {$certificate->resident->last_name}.",
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

        // Prepare Name Components with MR./MS.
        $genderTitle = strtoupper($resident->gender) === 'FEMALE' ? 'MS.' : 'MR.';
        $middleInitial = $resident->middle_name ? strtoupper(substr($resident->middle_name, 0, 1)) . '.' : '';
        
        $formalFullName = $genderTitle . ' ' . strtoupper($resident->first_name) . ' ' . ($middleInitial ? $middleInitial . ' ' : '') . strtoupper($resident->last_name);
        $standardFullName = strtoupper($resident->first_name) . ' ' . ($middleInitial ? $middleInitial . ' ' : '') . strtoupper($resident->last_name);

        // DETOUR: If it is a First Time Job Seeker, render the 2-page set
        if ($certificate->certificate_type == 'First Time Job Seeker') {
            $witnessName = session('witness_name', 'ROSEMARIE M. GALANGAM');
            $witnessTitle = session('witness_title', 'Barangay Secretary');
            
            return view('certificates.print-jobseeker', compact(
                'certificate', 
                'resident', 
                'settings', 
                'witnessName', 
                'witnessTitle', 
                'formalFullName', 
                'standardFullName'
            ));
        }

        // Map regular certificate types
        $config = [
            'Barangay Clearance' => 'BARANGAY CLEARANCE',
            'Certificate of Indigency' => 'CERTIFICATE OF INDIGENCY',
            'Certificate of Residency' => 'CERTIFICATE OF RESIDENCY',
        ];

        $certificateTitle = $config[$certificate->certificate_type] ?? 'BARANGAY CLEARANCE';
        $age = \Carbon\Carbon::parse($resident->birth_date)->age;
        $gender = strtolower($resident->gender);
        $civilStatus = strtolower($resident->civil_status);
        $purok = $resident->purok;
        $purpose = strtolower($certificate->purpose);

        return view('certificates.print', compact(
            'certificate', 
            'resident', 
            'settings', 
            'certificateTitle', 
            'formalFullName', 
            'standardFullName',
            'age', 
            'gender', 
            'civilStatus', 
            'purok', 
            'purpose'
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