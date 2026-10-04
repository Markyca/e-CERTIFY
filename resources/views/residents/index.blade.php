@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-md-4 py-3">

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h3 class="fw-bold mb-0">Residents</h3>
        <div class="d-flex gap-2">
            @if(auth()->check() && in_array(auth()->user()->role, ['Admin', 'Secretary']))
                <a href="{{ route('residents.archived') }}" class="btn btn-outline-secondary" title="View archive">
                    <i class="bi bi-archive me-1"></i> Archive
                </a>
            @endif
            <a href="{{ route('residents.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-1"></i> Add Resident
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('residents.index') }}" method="GET" id="residentFilters" class="row g-2 align-items-center">

                <div class="col-lg-4 col-md-12">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-primary border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search name…" value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-primary border-end-0" title="Purok"><i class="bi bi-geo-alt"></i></span>
                        <select name="purok" class="form-select border-start-0 ps-0" aria-label="Purok">
                            <option value="">All Puroks</option>
                            @foreach(range(1, 7) as $p)
                                <option value="{{ $p }}" {{ request('purok') == $p ? 'selected' : '' }}>Purok {{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-primary border-end-0" title="Gender"><i class="bi bi-gender-ambiguous"></i></span>
                        <select name="gender" class="form-select border-start-0 ps-0" aria-label="Gender">
                            <option value="">All Genders</option>
                            @foreach($genders as $g)
                                <option value="{{ $g }}" {{ request('gender') === $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-primary border-end-0" title="Civil status"><i class="bi bi-heart"></i></span>
                        <select name="civil_status" class="form-select border-start-0 ps-0" aria-label="Civil status">
                            <option value="">All Statuses</option>
                            @foreach($civilStatuses as $c)
                                <option value="{{ $c }}" {{ request('civil_status') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" title="Apply filters"><i class="bi bi-funnel-fill"></i></button>
                    <a href="{{ route('residents.index') }}" class="btn btn-light flex-fill border" title="Clear filters"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4 py-3">Name</th>
                            <th class="py-3">Purok</th>
                            <th class="py-3">Gender</th>
                            <th class="py-3">Civil Status</th>
                            <th class="text-end pe-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($residents as $resident)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark">{{ $resident->last_name }}, {{ $resident->first_name }}</div>
                                    @if($resident->middle_name)
                                        <div class="small text-muted">{{ $resident->middle_name }}</div>
                                    @endif
                                </td>
                                <td><span class="badge bg-primary bg-opacity-10">{{ $resident->purok }}</span></td>
                                <td>
                                    <i class="bi {{ strtolower($resident->gender) === 'female' ? 'bi-gender-female' : 'bi-gender-male' }} text-primary me-1"></i>{{ $resident->gender }}
                                </td>
                                <td>{{ $resident->civil_status }}</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('certificates.create', $resident->id) }}" class="btn btn-sm btn-outline-primary" title="Issue document">
                                            <i class="bi bi-file-earmark-plus me-1"></i> Issue
                                        </a>

                                        @if(auth()->check() && in_array(auth()->user()->role, ['Admin', 'Secretary']))
                                            <a href="{{ route('residents.edit', $resident->id) }}" class="btn btn-sm btn-light border text-primary" title="Edit profile">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form action="{{ route('residents.destroy', $resident->id) }}" method="POST" onsubmit="return confirm('Archive this resident? Their document history will be kept.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border text-warning" title="Archive profile">
                                                    <i class="bi bi-archive-fill"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    No residents found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination Footer -->
        <div id="residentPagination">
            @if($residents->hasPages())
                <div class="card-footer bg-white border-top py-3 px-4">
                    {{ $residents->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Live filtering -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('residentFilters');
        const fields = ['search', 'purok', 'gender', 'civil_status'].map(n => form.elements[n]);
        const tableContainer = document.querySelector('.table-responsive');
        const pagination = document.getElementById('residentPagination');

        let typingTimer;

        function fetchLiveResults() {
            const url = new URL(window.location.href);
            fields.forEach(f => {
                if (f.value) url.searchParams.set(f.name, f.value); else url.searchParams.delete(f.name);
            });
            url.searchParams.delete('page'); // new filter -> back to page 1

            fetch(url)
                .then(r => r.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    tableContainer.innerHTML = doc.querySelector('.table-responsive').innerHTML;
                    pagination.innerHTML = doc.getElementById('residentPagination').innerHTML;
                    window.history.pushState({}, '', url);
                });
        }

        // Typing is debounced; dropdowns apply immediately
        form.elements['search'].addEventListener('input', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(fetchLiveResults, 300);
        });
        ['purok', 'gender', 'civil_status'].forEach(n => form.elements[n].addEventListener('change', fetchLiveResults));

        // Enter should not reload the page, the list is already live
        form.addEventListener('submit', function (e) { e.preventDefault(); fetchLiveResults(); });
    });
</script>
@endsection
