<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resident;
use App\Models\Certificate;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Core Metrics (Updated with active vs archived counts)
        $totalResidents = Resident::where('is_active', true)->count();
        $totalArchived = Resident::where('is_active', false)->count(); // New metric for archived card
        
        $certificatesToday = Certificate::whereDate('created_at', Carbon::today())->count();
        $certificatesThisMonth = Certificate::whereMonth('created_at', Carbon::now()->month)
                                            ->whereYear('created_at', Carbon::now()->year)
                                            ->count();

        // 2. Chart Data (Certificates by Type this month)
        $chartData = Certificate::select('certificate_type', DB::raw('count(*) as total'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('certificate_type')
            ->pluck('total', 'certificate_type');

        $chartLabels = $chartData->keys();
        $chartValues = $chartData->values();

        // 3. Chart Data (Certificates by Type for Today)
        $dayData = Certificate::select('certificate_type', DB::raw('count(*) as total'))
            ->whereDate('created_at', Carbon::today())
            ->groupBy('certificate_type')
            ->pluck('total', 'certificate_type');

        $dayLabels = $dayData->keys();
        $dayValues = $dayData->values();

        // 4. Recent Document Issuances Feed (Last 5)
        $recentCertificates = Certificate::with('resident')->latest()->take(5)->get();

        // 5. Admin Audit Logs Feed (Only fetched if user is admin)
        $recentLogs = collect();
        $user = Auth::user();

        if ($user && $user->role === 'Admin') {
            $recentLogs = AuditLog::with('user')->latest()->take(5)->get();
        }

        // 6. Quick Resident Search Lookup
        $searchResults = collect();
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $searchResults = Resident::where('is_active', true)
                ->where(function($q) use ($searchTerm) {
                    $q->where('first_name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('last_name', 'LIKE', "%{$searchTerm}%");
                })
                ->take(5)
                ->get();
        }

        // 7. Fetch Barangay Settings for the Digital Signature Toggle
        $settings = \App\Models\BarangaySetting::first();

        return view('dashboard.index', compact(
            'totalResidents', 
            'totalArchived', // Pass to view
            'certificatesToday', 
            'certificatesThisMonth', 
            'chartLabels', 
            'chartValues', 
            'dayLabels',     // Pass to view
            'dayValues',     // Pass to view
            'recentCertificates', 
            'recentLogs',
            'searchResults',
            'settings'
        ));
    }

    public function searchResidents(Request $request)
    {
        $query = $request->input('q');
        
        $terms = collect(explode(' ', trim($query)))
                    ->filter() 
                    ->map(fn($term) => preg_replace('/[^A-Za-z0-9]/', '', $term)); 

        // Only search active residents in live dropdown
        $residents = Resident::where('is_active', true);

        foreach ($terms as $term) {
            $residents->where(function($q) use ($term) {
                $q->where('first_name', 'LIKE', "%{$term}%")
                ->orWhere('last_name', 'LIKE', "%{$term}%")
                ->orWhere('middle_name', 'LIKE', "%{$term}%");
            });
        }

        return response()->json($residents->limit(10)->get());
    }
}