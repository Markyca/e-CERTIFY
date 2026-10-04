@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center gap-2"><span class="stat-dot"><i class="bi bi-person-plus-fill"></i></span><span class="fw-bold fs-5">New Resident</span></div>

                <div class="card-body">
                    @if($errors->has('duplicate'))
                        <div class="alert alert-danger">
                            {{ $errors->first('duplicate') }}
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('residents.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name">
                            </div>
                            <div class="col-md-4">
                                <label for="extension_name" class="form-label">Extension Name (e.g., Jr.)</label>
                                <input type="text" class="form-control" id="extension_name" name="extension_name">
                            </div>
                            <div class="col-md-4">
                                <label for="birth_date" class="form-label">Birth Date *</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Gender *</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="" disabled selected>Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="civil_status" class="form-label">Civil Status *</label>
                                <select class="form-select" id="civil_status" name="civil_status" required>
                                    <option value="" disabled selected>Select</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="purok" class="form-label">Purok *</label>
                                <input type="text" class="form-control" id="purok" name="purok" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('residents.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Resident</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection