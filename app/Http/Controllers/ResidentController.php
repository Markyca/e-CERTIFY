<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 


class ResidentController extends Controller
{
    // Display the list of residents
   public function index(Request $request)
    {
        // FIX: Only fetch active (non-archived) residents
        $query = Resident::where('is_active', true);

        // 1. SMART SEARCH Function
        if ($request->filled('search')) {
            $searchTerms = explode(' ', $request->search);

            $query->where(function($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->where(function($subQuery) use ($term) {
                        $subQuery->where('first_name', 'LIKE', "%{$term}%")
                                ->orWhere('last_name', 'LIKE', "%{$term}%")
                                ->orWhere('middle_name', 'LIKE', "%{$term}%");
                    });
                }
            });
        }

        // 2. Filter by Purok
        if ($request->filled('purok')) {
            $query->where('purok', $request->purok);
        }

        // 3. Filter by Gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // 4. Filter by Civil Status
        if ($request->filled('civil_status')) {
            $query->where('civil_status', $request->civil_status);
        }

        // 5. Sort alphabetically and paginate
        $residents = $query->orderBy('last_name', 'ASC')
                        ->orderBy('first_name', 'ASC')
                        ->paginate(10)
                        ->withQueryString();

        // Filter options: the standard choices plus anything already stored
        $genders = collect(['Male', 'Female'])
            ->merge(Resident::where('is_active', true)->distinct()->pluck('gender'))
            ->filter()->unique()->values();
        $civilStatuses = collect(['Single', 'Married', 'Widowed'])
            ->merge(Resident::where('is_active', true)->distinct()->pluck('civil_status'))
            ->filter()->unique()->values();

        return view('residents.index', compact('residents', 'genders', 'civilStatuses'));
    }

    // Show the form to add a new resident
    public function create()
    {
        return view('residents.create');
    }

    // Store the new resident in the database
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'extension_name' => 'nullable|string|max:50',
            'birth_date' => 'required|date',
            'gender' => 'required|string',
            'civil_status' => 'required|string',
            'purok' => 'required|string|max:255',
        ]);

        // Check for existing duplicate resident
        $duplicate = Resident::where('first_name', $request->first_name)
                    ->where('last_name', $request->last_name)
                    ->where('birth_date', $request->birth_date)
                    ->exists();

        if ($duplicate) {
            return back()->withInput()->withErrors(['duplicate' => 'A resident with this exact name and birth date already exists in the system.']);
        }

        $resident = Resident::create($validatedData);

        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'RESIDENT_CREATED',
            'description' => "Added new resident master file for {$resident->first_name} {$resident->last_name}.",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('residents.index')->with('success', 'Resident added successfully.');
    }

    // Additional methods (show, edit, update, destroy) will go here later

    // Show the form to edit an existing resident
    public function edit(Resident $resident)
    {
        return view('residents.edit', compact('resident'));
    }

    // Update the resident in the database
    public function update(Request $request, Resident $resident)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'extension_name' => 'nullable|string|max:50',
            'birth_date' => 'required|date',
            'gender' => 'required|string',
            'civil_status' => 'required|string',
            'purok' => 'required|string|max:255',
        ]);

        $resident->update($validatedData);

        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'RESIDENT_UPDATED',
            'description' => "Updated resident profile information for {$resident->first_name} {$resident->last_name}.",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('residents.index')->with('success', 'Resident updated successfully.');
    }

    // Remove the resident from the database
    // Change or update this method in your ResidentController
    public function destroy(Resident $resident)
    {
        $fullName = "{$resident->first_name} {$resident->last_name}";
        
        // FIX: Archive instead of hard delete
        $resident->update(['is_active' => false]);

        // Record the audit log
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'RESIDENT_ARCHIVED',
            'description' => "Archived resident master file record for {$fullName}.",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('residents.index')->with('success', 'Resident archived successfully. Document history retained.');
    }

    // Display the list of archived residents
    public function archived(Request $request)
    {
        $query = Resident::where('is_active', false);

        if ($request->filled('search')) {
            $searchTerms = explode(' ', $request->search);
            $query->where(function($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->where(function($subQuery) use ($term) {
                        $subQuery->where('first_name', 'LIKE', "%{$term}%")
                                ->orWhere('last_name', 'LIKE', "%{$term}%")
                                ->orWhere('middle_name', 'LIKE', "%{$term}%");
                    });
                }
            });
        }

        $residents = $query->orderBy('last_name', 'ASC')
                        ->orderBy('first_name', 'ASC')
                        ->paginate(10)
                        ->withQueryString();

        return view('residents.archived', compact('residents'));
    }

    // Restore an archived resident back to active
    public function restore($id)
    {
        $resident = Resident::findOrFail($id);
        $resident->update(['is_active' => true]);

        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'RESIDENT_RESTORED',
            'description' => "Restored resident profile record for {$resident->first_name} {$resident->last_name}.",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('residents.archived')->with('success', 'Resident restored successfully.');
    }
}