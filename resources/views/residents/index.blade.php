@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 px-md-5">
    <div class="row justify-content-center">
        <div class="col-12">
            
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Resident Master File</h3>
                    <p class="text-muted small mb-0">Manage demographics and issue barangay certificates.</p>
                </div>
                <!-- Place next to your 'Add New Resident' button -->
                <div class="d-flex gap-2">
                    <!-- Only Admin and Secretary can see the View Archive button -->
                    @if(auth()->check() && in_array(auth()->user()->role, ['Admin', 'Secretary']))
                        <a href="{{ route('residents.archived') }}" class="btn btn-outline-secondary shadow-sm fw-semibold">
                            <i class="bi bi-archive me-1"></i> View Archive
                        </a>
                    @endif
                    <a href="{{ route('residents.create') }}" class="btn btn-primary shadow-sm fw-semibold">
                        <i class="bi bi-person-plus-fill me-1"></i> Add New Resident
                    </a>
                </div>
            </div>

            <!-- Search & Filter Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <form action="{{ route('residents.index') }}" method="GET" class="row g-3 align-items-end">
                        
                        <!-- Search Bar -->
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Search Resident</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search by first or last name..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Purok Dropdown -->
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Filter by Purok</label>
                            <select name="purok" class="form-select">
                                <option value="">All Puroks</option>
                                <!-- Adjust these options based on your Barangay's actual Puroks -->
                                <option value="1" {{ request('purok') == '1' ? 'selected' : '' }}>Purok 1</option>
                                <option value="2" {{ request('purok') == '2' ? 'selected' : '' }}>Purok 2</option>
                                <option value="3" {{ request('purok') == '3' ? 'selected' : '' }}>Purok 3</option>
                                <option value="4" {{ request('purok') == '4' ? 'selected' : '' }}>Purok 4</option>
                                <option value="5" {{ request('purok') == '5' ? 'selected' : '' }}>Purok 5</option>
                                <option value="6" {{ request('purok') == '6' ? 'selected' : '' }}>Purok 6</option>
                                <option value="7" {{ request('purok') == '7' ? 'selected' : '' }}>Purok 7</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                            <a href="{{ route('residents.index') }}" class="btn btn-light w-100">Clear</a>
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
                                    <th class="py-3 font-weight-semibold">Purok</th>
                                    <th class="py-3 font-weight-semibold">Gender</th>
                                    <th class="py-3 font-weight-semibold">Civil Status</th>
                                    <th class="text-end pe-4 py-3 font-weight-semibold">Actions</th>
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
                                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $resident->purok }}</span></td>
                                        <td>{{ $resident->gender }}</td>
                                        <td>{{ $resident->civil_status }}</td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <!-- Issue Document Button (Visible to Everyone) -->
                                                <a href="{{ route('certificates.create', $resident->id) }}" class="btn btn-sm btn-outline-primary fw-semibold" title="Issue Document">
                                                    <i class="bi bi-file-earmark-text"></i> Issue
                                                </a>
                                                
                                                <!-- Only Admin and Secretary can see Edit and Delete -->
                                                @if(auth()->check() && in_array(auth()->user()->role, ['Admin', 'Secretary']))
                                                    <!-- Edit Button -->
                                                    <a href="{{ route('residents.edit', $resident->id) }}" class="btn btn-sm btn-light text-primary border" title="Edit Profile">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>

                                                    <!-- Archive Button -->
                                                    <form action="{{ route('residents.destroy', $resident->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to archive this resident profile? Their document history will be preserved.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light text-warning border" title="Archive Profile">
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
                                            No resident records found in the database.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination Footer -->
                @if($residents->hasPages())
                    <div class="card-footer bg-white border-top py-3 px-4">
                        {{ $residents->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<!-- Live Search Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        const purokSelect = document.querySelector('select[name="purok"]');
        const tableContainer = document.querySelector('.table-responsive');

        // Timer for "debouncing" (prevents crashing the server by waiting for the user to stop typing)
        let typingTimer;
        const doneTypingInterval = 300; // 300 milliseconds

        function fetchLiveResults() {
            // 1. Get current search values and build the URL
            const url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('purok', purokSelect.value);
            url.searchParams.delete('page'); // Reset to page 1 on a new search

            // 2. Fetch the new HTML quietly in the background
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    // 3. Convert the response into an HTML document
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // 4. Extract the new table and replace the old one on the screen
                    const newTable = doc.querySelector('.table-responsive').innerHTML;
                    tableContainer.innerHTML = newTable;

                    // 5. Update the browser URL bar without reloading the page
                    window.history.pushState({}, '', url);
                });
        }

        // Trigger the search when typing in the search box
        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(fetchLiveResults, doneTypingInterval);
        });

        // Trigger the search immediately when the Purok dropdown is changed
        purokSelect.addEventListener('change', function() {
            fetchLiveResults();
        });
    });
</script>
@endsection