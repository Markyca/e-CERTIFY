@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    
    <!-- Page Header & Live Search Bar -->
    <div class="row align-items-center mb-4 g-3">
        <div class="col-xl-5 col-lg-4">
            <h3 class="fw-bold text-dark mb-1">Barangay Dashboard</h3>
            <p class="text-muted small mb-0">Welcome back, {{ auth()->user()->name }}! Here is your operational overview.</p>
        </div>
        
        <!-- Live Resident Lookup Search Bar -->
        <div class="col-xl-7 col-lg-8 position-relative">
            <div class="input-group shadow-sm">
                <input type="text" id="liveSearchInput" class="form-control form-control-sm border-0 py-2 ps-3" placeholder="🔍 Live Resident Lookup (Type name to search)..." autocomplete="off">
                <button class="btn btn-primary btn-sm px-3" type="button" disabled>Search</button>
            </div>

            <!-- Live Dropdown Results Box -->
            <div id="searchResultsDropdown" class="position-absolute bg-white shadow-lg rounded-3 border p-2 mt-1 d-none" style="z-index: 1050; width: calc(100% - 0.75rem);">
                <h6 class="text-muted small fw-bold px-2 mb-1">Matching Residents</h6>
                <div id="resultsContainer"></div>
            </div>
        </div>
    </div>

    <!-- Metric Cards Row (Now 4 Cards with Correct Controller Parameters) -->
    <div class="row g-4 mb-4">
        <!-- Total Residents Card -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('residents.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 card-hover-effect">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 text-primary rounded-4 p-3 fs-3">👥</div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted small text-uppercase fw-semibold mb-1">Total Residents</h6>
                            <h3 class="fw-bold text-dark mb-0">{{ number_format($totalResidents) }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Archived Residents Card (Admin & Secretary Only) -->
        @if(in_array(auth()->user()->role, ['Admin', 'Secretary']))
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('residents.archived') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 card-hover-effect">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-secondary bg-opacity-10 text-secondary rounded-4 p-3 fs-3">📦</div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted small text-uppercase fw-semibold mb-1">Archived Residents</h6>
                            <h3 class="fw-bold text-dark mb-0">{{ number_format($totalArchived ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif

        <!-- Certificates Today Card (Sends ?date=YYYY-MM-DD) -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('certificates.history', ['date' => now()->toDateString()]) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 card-hover-effect">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 text-success rounded-4 p-3 fs-3">📄</div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted small text-uppercase fw-semibold mb-1">Certificates Today</h6>
                            <h3 class="fw-bold text-dark mb-0">{{ number_format($certificatesToday) }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Issued This Month Card (Sends ?month=X&year=YYYY) -->
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('certificates.history', ['month' => now()->month, 'year' => now()->year]) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 card-hover-effect">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 text-warning rounded-4 p-3 fs-3">📊</div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted small text-uppercase fw-semibold mb-1">Issued This Month</h6>
                            <h3 class="fw-bold text-dark mb-0">{{ number_format($certificatesThisMonth) }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Action Shortcuts & Toggle Grouped Together -->
    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h6 class="fw-bold text-dark mb-0">Quick Action Shortcuts</h6>
            
            <div class="d-flex flex-wrap align-items-center gap-3">
                <!-- Action Buttons -->
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('residents.create') }}" class="btn btn-primary btn-sm">+ Register New Resident</a>
                    <a href="{{ route('certificates.history') }}" class="btn btn-outline-secondary btn-sm">View Document History</a>
                    @if(in_array(auth()->user()->role, ['Admin', 'Secretary']))
                        <div class="dropdown">
                            <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                View Certificate Templates
                            </button>
                            <ul class="dropdown-menu shadow-sm border-0">
                                <li><a class="dropdown-item small" href="{{ route('templates.edit', 'clearance') }}">Barangay Clearance</a></li>
                                <li><a class="dropdown-item small" href="{{ route('templates.edit', 'indigency') }}">Certificate of Indigency</a></li>
                                <li><a class="dropdown-item small" href="{{ route('templates.edit', 'residency') }}">Certificate of Residency</a></li>
                                <li><a class="dropdown-item small" href="{{ route('templates.edit', 'jobseeker') }}">Job Seeker Certificate</a></li>
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Inline Digital Signature Switch -->
                <div class="form-check form-switch m-0 d-flex align-items-center gap-2 border-start ps-3">
                    <input class="form-check-input m-0" type="checkbox" role="switch" id="showSignatureSwitch" {{ optional($settings)->show_signature ? 'checked' : '' }} style="cursor: pointer; width: 2.5em; height: 1.25em;">
                    <label class="form-check-label small fw-semibold text-dark text-nowrap user-select-none" for="showSignatureSwitch" style="cursor: pointer;">
                        Digital Signature
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Section: Dual Analytics Charts (This Month & This Day) -->
    <div class="row g-4 mb-4">
        <!-- This Month Distribution Chart -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0">Certificate Distribution (This Month)</h6>
                    <span class="badge bg-light text-secondary border">Click bar to filter</span>
                </div>
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="certificateMonthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- This Day Distribution Chart -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0">Certificate Distribution (This Day)</h6>
                    <span class="badge bg-light text-secondary border">Click bar to filter</span>
                </div>
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="certificateDayChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Split Operational Feeds -->
    <div class="row g-4 mb-4">
        <div class="{{ auth()->user()->role === 'Admin' ? 'col-xl-6' : 'col-12' }}">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h6 class="fw-bold text-dark mb-3">Recent Certificate Issuances</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0 text-nowrap">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>Control No.</th>
                                <th>Resident Name</th>
                                <th>Type</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCertificates as $cert)
                                <tr>
                                    <td><span class="font-monospace small">{{ $cert->control_number }}</span></td>
                                    <td>{{ optional($cert->resident)->last_name }}, {{ optional($cert->resident)->first_name }}</td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-dark">{{ $cert->certificate_type }}</span></td>
                                    <td class="text-muted small">{{ $cert->created_at->format('M d, h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3 small">No recent certificates issued.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(auth()->user()->role === 'Admin')
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h6 class="fw-bold text-dark mb-3">System Audit Trail Feed (Admin Oversight)</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogs as $log)
                                <tr>
                                    <td class="text-nowrap">{{ optional($log->user)->name ?? 'System' }}</td>
                                    <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $log->action }}</span></td>
                                    <td class="text-muted small text-nowrap">{{ $log->created_at->format('M d, h:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3 small">No recent logs recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

</div>

<!-- Custom CSS for Card Hover Effect -->
<style>
    .card-hover-effect {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-hover-effect:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>

<!-- Chart.js CDN Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Define base variables safely rendered from PHP
    const historyBaseUrl = "{{ route('certificates.history') }}";
    const currentMonth = Number("{{ now()->month }}");
    const currentYear = Number("{{ now()->year }}");
    const todayDate = "{{ now()->toDateString() }}";

    // 1. Month Chart Setup & Click Interactivity
    const ctxMonth = document.getElementById('certificateMonthChart').getContext('2d');
    const monthLabels = JSON.parse('{!! json_encode($chartLabels) !!}');
    const monthValues = JSON.parse('{!! json_encode($chartValues) !!}');

    const certificateMonthChart = new Chart(ctxMonth, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Count Issued',
                data: monthValues,
                backgroundColor: 'rgba(13, 110, 253, 0.75)',
                borderColor: 'rgb(13, 110, 253)',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const certType = monthLabels[index];
                    window.location.href = historyBaseUrl + "?month=" + currentMonth + "&year=" + currentYear + "&type=" + encodeURIComponent(certType);
                }
            },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { display: false } }
        }
    });

    // 2. Day Chart Setup & Click Interactivity
    const ctxDay = document.getElementById('certificateDayChart').getContext('2d');
    const dayLabels = JSON.parse('{!! json_encode($dayLabels ?? []) !!}');
    const dayValues = JSON.parse('{!! json_encode($dayValues ?? []) !!}');

    const certificateDayChart = new Chart(ctxDay, {
        type: 'bar',
        data: {
            labels: dayLabels,
            datasets: [{
                label: 'Count Issued',
                data: dayValues,
                backgroundColor: 'rgba(25, 135, 84, 0.75)',
                borderColor: 'rgb(25, 135, 84)',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const certType = dayLabels[index];
                    window.location.href = historyBaseUrl + "?date=" + todayDate + "&type=" + encodeURIComponent(certType);
                }
            },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { legend: { display: false } }
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('liveSearchInput');
        const dropdown = document.getElementById('searchResultsDropdown');
        const container = document.getElementById('resultsContainer');

        searchInput.addEventListener('input', function() {
            let query = this.value.trim();

            if (query.length < 2) {
                dropdown.classList.add('d-none');
                container.innerHTML = '';
                return;
            }

            fetch(`{{ route('dashboard.search-residents') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    container.innerHTML = '';
                    if (data.length > 0) {
                        dropdown.classList.remove('d-none');
                        data.forEach(resident => {
                            let item = `
                                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                    <div>
                                        <span class="fw-semibold text-dark">${resident.last_name}, ${resident.first_name}</span>
                                        <span class="text-muted small d-block">Purok {{ '$resident->purok' ?? 'N/A' }}</span>
                                    </div>
                                    <a href="/residents/${resident.id}/certificates/create" class="btn btn-sm btn-primary py-0 px-2">Issue Document</a>
                                </div>
                            `;
                            container.innerHTML += item;
                        });
                    } else {
                        dropdown.classList.remove('d-none');
                        container.innerHTML = `<p class="text-muted small text-center mb-0 py-2">No residents found.</p>`;
                    }
                });
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('d-none');
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const signatureSwitch = document.getElementById('showSignatureSwitch');

        if (signatureSwitch) {
            signatureSwitch.addEventListener('change', function () {
                const isChecked = this.checked ? 1 : 0;

                fetch("{{ route('settings.update.ajax') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ show_signature: isChecked })
                })
                .then(response => response.json())
                .catch(error => console.error('Error updating signature setting:', error));
            });
        }
    });
</script>
@endsection