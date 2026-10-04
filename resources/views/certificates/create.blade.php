@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center gap-2">
                    <span class="stat-dot"><i class="bi bi-file-earmark-plus-fill"></i></span>
                    <span class="fw-bold fs-5"><strong>{{ $resident->first_name }} {{ $resident->last_name }}</strong></span>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger pb-0">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('certificates.store') }}">
                        @csrf
                        <input type="hidden" name="resident_id" value="{{ $resident->id }}">

                        <div class="mb-4">
                            <label for="certificate_type" class="form-label">Select Document Type *</label>
                            <select class="form-select form-select-lg" id="certificate_type" name="certificate_type" required>
                                <option value="" disabled selected>-- Choose Certificate --</option>
                                <option value="Certificate of Residency">Certificate of Residency</option>
                                <option value="Certificate of Indigency">Certificate of Indigency</option>
                                <option value="Barangay Clearance">Barangay Clearance</option>
                                <option value="First Time Job Seeker">First Time Job Seeker (Certification + Oath)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="purpose" class="form-label">Purpose of Request *</label>
                            <input type="text" class="form-control" id="purpose" name="purpose" placeholder="e.g., Employment, Scholarship, Bank Requirement" required>
                        </div>

                        <!-- Conditional Witness Fields for Job Seeker -->
                        <div id="jobSeekerWitnessFields" class="card bg-light p-3 mb-4 d-none">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary fw-bold m-0"><i class="bi bi-person-badge me-1"></i> First-Time Job Seeker Witness Details</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="setDefaultWitness">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Use Default
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <label for="witness_name" class="form-label">Witness Name *</label>
                                <input type="text" class="form-control" id="witness_name" name="witness_name" value="{{ old('witness_name', $brgy->witness()['name']) }}" placeholder="e.g., JUAN D. DELA CRUZ">
                            </div>

                            <div class="mb-3">
                                <label for="witness_title" class="form-label">Witness Title *</label>
                                <input type="text" class="form-control" id="witness_title" name="witness_title" value="{{ old('witness_title', $brgy->witness()['title']) }}" placeholder="e.g., Barangay Secretary">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('residents.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Generate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Direct script block so it runs without needing layout stack configuration -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const certTypeSelect = document.getElementById('certificate_type');
        const witnessFields = document.getElementById('jobSeekerWitnessFields');
        const witnessNameInput = document.getElementById('witness_name');
        const witnessTitleInput = document.getElementById('witness_title');
        const defaultBtn = document.getElementById('setDefaultWitness');

        function toggleWitnessFields() {
            if (certTypeSelect.value === 'First Time Job Seeker') {
                witnessFields.classList.remove('d-none');
                witnessNameInput.setAttribute('required', 'required');
                witnessTitleInput.setAttribute('required', 'required');
            } else {
                witnessFields.classList.add('d-none');
                witnessNameInput.removeAttribute('required');
                witnessTitleInput.removeAttribute('required');
            }
        }

        certTypeSelect.addEventListener('change', toggleWitnessFields);

        // Default Secretary Quick Fill Button
        defaultBtn.addEventListener('click', function () {
            witnessNameInput.value = @json($brgy->witness()['name']);
            witnessTitleInput.value = @json($brgy->witness()['title']);
        });

        // Run on load in case of validation redirect
        toggleWitnessFields();
    });
</script>
@endsection