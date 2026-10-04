@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 px-md-5">
    <div class="row justify-content-center">
        <div class="col-12">
            
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">Users <span class="hint fs-6" data-bs-toggle="tooltip" title="Manage system access, roles, and administrative accounts."><i class="bi bi-info-circle"></i></span></h3>
                </div>
                <div>
                    <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm btn-sm">
                        <i class="bi bi-person-plus me-1"></i> Add New User
                    </a>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <form id="userFilterForm" action="{{ route('users.index') }}" method="GET" class="row g-3 align-items-end">
                        
                        <!-- Role Filter -->
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-semibold">Role</label>
                            <select name="role" class="form-select form-select-sm">
                                <option value="">All Roles</option>
                                <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="Secretary" {{ request('role') == 'Secretary' ? 'selected' : '' }}>Secretary</option>
                                <option value="Staff" {{ request('role') == 'Staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-semibold">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Reset Button -->
                        <div class="col-md-4 text-end">
                            <a href="{{ route('users.index') }}" class="btn btn-light btn-sm text-secondary w-100">
                                <i class="bi bi-x-circle me-1"></i> Reset Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3 font-weight-semibold">Name</th>
                                    <th class="py-3 font-weight-semibold">Email</th>
                                    <th class="py-3 font-weight-semibold">Role</th>
                                    <th class="py-3 font-weight-semibold">Status</th>
                                    <th class="text-end pe-4 py-3 font-weight-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse ($users as $user)
                                    <tr>
                                        <!-- Name Column -->
                                        <td class="ps-4 py-3">
                                            <div class="fw-semibold text-dark">
                                                <i class="bi bi-person-circle text-muted me-2 fs-5 align-middle"></i> 
                                                {{ $user->name }}
                                            </div>
                                        </td>
                                        
                                        <!-- Email Column -->
                                        <td>
                                            <div class="text-secondary small">
                                                {{ $user->email }}
                                            </div>
                                        </td>
                                        
                                        <!-- Role Badge Column -->
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-3 py-2">
                                                {{ $user->role ?? 'Standard User' }}
                                            </span>
                                        </td>
                                        
                                        <!-- Status Badge Column -->
                                        <td>
                                            @if($user->is_active ?? true)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-2">
                                                    Active
                                                </span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-2">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <!-- Actions Column -->
                                        <td class="text-end pe-4">
                                            <div class="btn-group" role="group">
                                                <!-- Edit User Button -->
                                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit User">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                
                                                <!-- Deactivate / Activate Form -->
                                                <form action="{{ route('users.toggleStatus', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to change this user status?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if($user->is_active)
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Deactivate User">
                                                            <i class="bi bi-unlock-fill"></i>
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Activate User">
                                                            <i class="bi bi-lock-fill"></i>
                                                        </button>
                                                    @endif
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-people fs-1 d-block mb-3 text-black-50"></i>
                                            <h6 class="fw-semibold text-dark">No users found</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination Footer -->
                @if($users->hasPages())
                    <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-end">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</div>

<!-- Live Filter Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById('userFilterForm');
        if (!form) return;

        const inputs = form.querySelectorAll('select');
        inputs.forEach(input => {
            input.addEventListener('change', function () {
                form.submit();
            });
        });
    });
</script>
@endsection