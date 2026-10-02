@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-primary mb-0">Edit User: {{ $user->name }}</h5>
                    <p class="text-muted small">Update account details or change their system role.</p>
                </div>
                <div class="card-body px-4 pb-4">
                    
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold">System Role</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="Staff" {{ $user->role === 'Staff' ? 'selected' : '' }}>Staff</option>
                                <option value="Secretary" {{ $user->role === 'Secretary' ? 'selected' : '' }}>Secretary</option>
                                <option value="Admin" {{ $user->role === 'Admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-info-circle me-1"></i> Leave password fields blank if you do not wish to change the user's password.
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">New Password (Optional)</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection