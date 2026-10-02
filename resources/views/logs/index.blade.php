@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 px-md-5">
    <div class="row justify-content-center">
        <div class="col-12">
            
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">System Audit Logs</h3>
                    <p class="text-muted small mb-0">Track administrative activity and document issuance events.</p>
                </div>
                <div>
                    <a href="{{ route('logs.print', request()->all()) }}" class="btn btn-outline-secondary shadow-sm btn-sm" target="_blank">
                        <i class="bi bi-printer me-1"></i> Print Report
                    </a>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <form id="auditFilterForm" action="{{ route('logs.index') }}" method="GET" class="row g-3 align-items-end">
                        
                        <!-- User Filter -->
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-semibold">User</label>
                            <select name="user_id" class="form-select form-select-sm">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Filter -->
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-semibold">Action Type</label>
                            <select name="action" class="form-select form-select-sm">
                                <option value="">All Actions</option>
                                <optgroup label="User & Auth">
                                    <option value="USER_CREATED" {{ request('action') == 'USER_CREATED' ? 'selected' : '' }}>USER_CREATED</option>
                                    <option value="USER_UPDATED" {{ request('action') == 'USER_UPDATED' ? 'selected' : '' }}>USER_UPDATED</option>
                                    <option value="USER_STATUS_CHANGED" {{ request('action') == 'USER_STATUS_CHANGED' ? 'selected' : '' }}>USER_STATUS_CHANGED</option>
                                    <option value="USER_LOGIN" {{ request('action') == 'USER_LOGIN' ? 'selected' : '' }}>USER_LOGIN</option>
                                    <option value="USER_LOGOUT" {{ request('action') == 'USER_LOGOUT' ? 'selected' : '' }}>USER_LOGOUT</option>
                                </optgroup>
                                <optgroup label="Residents & Archives">
                                    <option value="RESIDENT_CREATED" {{ request('action') == 'RESIDENT_CREATED' ? 'selected' : '' }}>RESIDENT_CREATED</option>
                                    <option value="RESIDENT_UPDATED" {{ request('action') == 'RESIDENT_UPDATED' ? 'selected' : '' }}>RESIDENT_UPDATED</option>
                                    <option value="RESIDENT_ARCHIVED" {{ request('action') == 'RESIDENT_ARCHIVED' ? 'selected' : '' }}>RESIDENT_ARCHIVED</option>
                                    <option value="RESIDENT_RESTORED" {{ request('action') == 'RESIDENT_RESTORED' ? 'selected' : '' }}>RESIDENT_RESTORED</option>
                                    <option value="RESIDENT_DELETED" {{ request('action') == 'RESIDENT_DELETED' ? 'selected' : '' }}>RESIDENT_DELETED</option>
                                </optgroup>
                                <optgroup label="Certificates & System Settings">
                                    <option value="CERTIFICATE_ISSUED" {{ request('action') == 'CERTIFICATE_ISSUED' ? 'selected' : '' }}>CERTIFICATE_ISSUED</option>
                                    <option value="SETTINGS_UPDATED" {{ request('action') == 'SETTINGS_UPDATED' ? 'selected' : '' }}>SETTINGS_UPDATED</option>
                                    <option value="SIGNATURE_TOGGLE" {{ request('action') == 'SIGNATURE_TOGGLE' ? 'selected' : '' }}>SIGNATURE_TOGGLE</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Specific Date Filter -->
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-semibold">Specific Date</label>
                            <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
                        </div>

                        <!-- Month Filter -->
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-semibold">Month</label>
                            <select name="month" class="form-select form-select-sm">
                                <option value="">All Months</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-semibold">Year</label>
                            <select name="year" class="form-select form-select-sm">
                                <option value="">All Years</option>
                                @foreach(range(date('Y'), 2023) as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Clear Filter Button -->
                        <div class="col-12 text-end">
                            <a href="{{ route('logs.index') }}" class="btn btn-light btn-sm text-secondary">
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
                                    <th class="ps-4 py-3 fw-semibold">Timestamp</th>
                                    <th class="py-3 fw-semibold">User</th>
                                    <th class="py-3 fw-semibold">Action</th>
                                    <th class="py-3 fw-semibold">Description</th>
                                    <th class="text-end pe-4 py-3 fw-semibold">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="ps-4 text-muted small">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="fw-semibold">{{ $log->user->name ?? 'System' }}</td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-dark border">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td>{{ $log->description }}</td>
                                        <td class="pe-4 text-muted small text-end">{{ $log->ip_address }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-clipboard-x display-6 d-block mb-2"></i>
                                                <p class="mb-0">No records found</p>
                                                <small>No audit logs match your selected filter parameters.</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination Footer -->
                @if($logs->hasPages())
                    <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-end">
                        {{ $logs->links('pagination::bootstrap-5') }}
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</div>

<!-- Live Filter Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById('auditFilterForm');
        if (!form) return;

        const inputs = form.querySelectorAll('select, input');
        inputs.forEach(input => {
            input.addEventListener('change', function () {
                form.submit();
            });
        });
    });
</script>
@endsection