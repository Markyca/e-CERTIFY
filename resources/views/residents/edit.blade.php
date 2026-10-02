@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Resident Profile</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('residents.update', $resident->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $resident->first_name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $resident->last_name }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ $resident->middle_name }}">
                            </div>
                            <div class="col-md-4">
                                <label for="extension_name" class="form-label">Extension Name</label>
                                <input type="text" class="form-control" id="extension_name" name="extension_name" value="{{ $resident->extension_name }}">
                            </div>
                            <div class="col-md-4">
                                <label for="birth_date" class="form-label">Birth Date *</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ $resident->birth_date }}" required>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="gender" class="form-label">Gender *</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="Male" {{ $resident->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $resident->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="civil_status" class="form-label">Civil Status *</label>
                                <select class="form-select" id="civil_status" name="civil_status" required>
                                    <option value="Single" {{ $resident->civil_status == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ $resident->civil_status == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ $resident->civil_status == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="purok" class="form-label">Purok *</label>
                                <input type="text" class="form-control" id="purok" name="purok" value="{{ $resident->purok }}" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('residents.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Resident</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection