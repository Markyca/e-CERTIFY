<?php

namespace App\Http\Controllers;

use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Order by specific role hierarchy: Admin -> Secretary -> Staff (then by name)
        $query->orderByRaw("FIELD(role, 'Admin', 'Secretary', 'Staff') ASC")
            ->orderBy('name', 'ASC');

        $users = $query->paginate(10)->appends($request->query());

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:Admin,Secretary,Staff'],
        ]);

        // Create the user once and assign to $newUser
        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => 1,
        ]);

        // Record the audit log safely using $newUser
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'USER_CREATED',
            'description' => "Created a new {$newUser->role} account for {$newUser->name}.",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('users.index')->with('success', 'User account created successfully.');
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', 'in:Admin,Secretary,Staff'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $oldRole = $user->role;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // ADD AUDIT LOG HERE
        $roleNote = ($oldRole !== $user->role) ? " (Role changed from {$oldRole} to {$user->role})" : "";
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'USER_UPDATED',
            'description' => "Updated user account details for {$user->name}{$roleNote}.",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('users.index')->with('success', 'User account updated successfully.');
    }

    /**
     * Remove the specified resource from storage (Replaced with status toggle).
     */
    public function destroy(User $user)
    {
        // Kept empty or redirected just in case, since we use toggleStatus now
        return redirect()->route('users.index');
    }

    /**
     * Toggle active/deactive status.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'activated' : 'deactivated';

        // ADD AUDIT LOG HERE
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'USER_STATUS_CHANGED',
            'description' => "Changed user account status for {$user->name} to {$statusText}.",
            'ip_address' => request()->ip(),
        ]);
        
        return back()->with('success', "User account has been successfully {$statusText}.");
    }
}