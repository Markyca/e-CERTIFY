@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 px-md-5">
    <div class="row justify-content-center">
        <div class="col-12">
            
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">History <span class="hint fs-6" data-bs-toggle="tooltip" title="Track and filter all certificates issued by the barangay."><i class="bi bi-info-circle"></i></span></h3>
                </div>
                <div>
                    <a href="{{ route('certificates.history.print', request()->all()) }}" class="btn btn-outline-secondary shadow-sm btn-sm" target="_blank">
                        <i class="bi bi-printer me-1"></i> Print Report
                    </a>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <form id="filterForm" action="{{ route('certificates.history') }}" method="GET" class="row g-3 align-items-end">
                        
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Specific Date</label>
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small text-muted">Month</label>
                            <select name="month" class="form-select">
                                <option value="">All Months</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small text-muted">Year</label>
                            <select name="year" class="form-select">
                                <option value="">All Years</option>
                                @foreach(range(date('Y'), 2023) as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Document Type</label>
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="Certificate of Residency" {{ request('type') == 'Certificate of Residency' ? 'selected' : '' }}>Residency</option>
                                <option value="Certificate of Indigency" {{ request('type') == 'Certificate of Indigency' ? 'selected' : '' }}>Indigency</option>
                                <option value="Barangay Clearance" {{ request('type') == 'Barangay Clearance' ? 'selected' : '' }}>Clearance</option>
                                <option value="First Time Job Seeker" {{ request('type') == 'First Time Job Seeker' ? 'selected' : '' }}>Job Seeker</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                            <a href="{{ route('certificates.history') }}" class="btn btn-light w-100">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3 font-weight-semibold">Control No.</th>
                                    <th class="py-3 font-weight-semibold">Resident</th>
                                    <th class="py-3 font-weight-semibold">Document Type</th>
                                    <th class="py-3 font-weight-semibold">Date Issued</th>
                                    <th class="text-end pe-4 py-3 font-weight-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse ($certificates as $cert)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <span class="font-monospace small text-muted bg-light px-2 py-1 rounded">
                                                {{ $cert->control_number }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">
                                                {{ $cert->resident->first_name ?? '' }} {{ $cert->resident->last_name ?? '' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-3 py-2">
                                                {{ $cert->certificate_type }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small text-muted fw-medium">
                                                {{ $cert->created_at->format('M d, Y') }} <br>
                                                <span class="opacity-75">{{ $cert->created_at->format('h:i A') }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('certificates.show', $cert->id) }}" class="btn btn-sm btn-outline-secondary" title="View/Print">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-file-earmark-x fs-1 d-block mb-3 text-black-50"></i>
                                            <h6 class="fw-semibold text-dark">No records found</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if(method_exists($certificates, 'hasPages') && $certificates->hasPages())
                    <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-end">
                        {{ $certificates->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
            
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById('filterForm');
        if (!form) return;

        const elements = form.querySelectorAll('input, select');
        elements.forEach(element => {
            element.addEventListener('change', function () {
                form.submit();
            });
        });
    });
</script>
@endsection