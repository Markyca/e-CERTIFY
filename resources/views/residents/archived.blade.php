@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 px-md-5">
    <div class="row justify-content-center">
        <div class="col-12">
            
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Archived Residents</h3>
                    <p class="text-muted small mb-0">Manage and restore previously archived resident profiles.</p>
                </div>
                <a href="{{ route('residents.index') }}" class="btn btn-outline-secondary shadow-sm fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Active Residents
                </a>
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
                                            @if(auth()->check() && in_array(auth()->user()->role, ['Admin', 'Secretary']))
                                                <form action="{{ route('residents.restore', $resident->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Restore this resident back to active status?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-light text-success border fw-semibold" title="Restore Profile">
                                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-archive fs-2 d-block mb-2"></i>
                                            No archived resident records found.
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
@endsection