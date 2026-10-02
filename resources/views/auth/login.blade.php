@extends('layouts.app')

@section('content')
<style>
    /* Custom enhancements for a premium feel */
    body {
        background-color: #f4f7f6; /* Soft light background */
    }
    .login-card {
        border-radius: 1rem;
        overflow: hidden;
    }
    .brand-section {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        position: relative;
    }
    .brand-section::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,%3Csvg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z" fill="%23ffffff" fill-opacity="0.05" fill-rule="evenodd"/%3E%3C/svg%3E');
    }
    .logo-placeholder {
        width: 75px; 
        height: 75px; 
        border-radius: 50%; 
        background-color: #ffffff; 
        color: #0d6efd; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 11px; 
        font-weight: bold;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        border: 3px solid rgba(255, 255, 255, 0.8);
    }
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-xl-9 col-lg-10">
            <div class="card login-card shadow-lg border-0">
                <div class="row g-0">
                    
                    <!-- Left Side: System Branding (Hidden on small screens) -->
                    <div class="col-md-6 d-none d-md-flex brand-section flex-column justify-content-center align-items-center text-white p-5 text-center">
                        <div class="d-flex justify-content-center align-items-center mb-4 gap-3 z-1">
                            <!-- LGU Logo -->
                            <div style="width: 85px; height: 85px; flex-shrink: 0;">
                                @if(isset($settings) && $settings->lgu_logo)
                                    <img src="{{ asset('storage/' . $settings->lgu_logo) }}" alt="LGU Logo" style="width: 100%; height: 100%; object-fit: contain; mix-blend-mode: multiply;">
                                @else
                                    <div class="border rounded-circle w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="font-size: 8px;">LGU Logo</div>
                                @endif
                            </div>

                            <!-- Barangay Logo -->
                            <div style="width: 85px; height: 85px; flex-shrink: 0;">
                                @if(isset($settings) && $settings->brgy_logo)
                                    <img src="{{ asset('storage/' . $settings->brgy_logo) }}" alt="Brgy Logo" style="width: 100%; height: 100%; object-fit: contain;">
                                @else
                                    <div class="border rounded-circle w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="font-size: 8px;">Brgy Logo</div>
                                @endif
                            </div>
                        </div>
                        
                        <h2 class="fw-bold mb-2 z-1" style="letter-spacing: 2px;">e-CERTIFY</h2>
                        <h6 class="mb-4 text-white-50 z-1 text-uppercase tracking-wide">Certificate Issuance System</h6>
                        
                        <p class="small mb-0 z-1 fw-light border-top border-light pt-3 mt-3 w-75">
                            Barangay Siempre Viva Sur<br>
                            <span class="opacity-75">Official Portal</span>
                        </p>
                    </div>

                    <!-- Right Side: Login Form -->
                    <div class="col-md-6 bg-white p-4 p-sm-5 d-flex flex-column justify-content-center">
                        
                        <!-- Mobile Branding (Visible only on small screens) -->
                        <div class="d-md-none text-center mb-4">
                            <h3 class="fw-bold text-primary mb-0">e-CERTIFY</h3>
                            <p class="small text-muted">Brgy. Siempre Viva Sur</p>
                        </div>

                        <div class="mb-4">
                            <h4 class="fw-bold text-dark mb-1">Welcome Back</h4>
                            <p class="text-muted small">Please sign in to your account to continue.</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-floating mb-3">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="name@example.com">
                                <label for="email" class="text-muted">Email Address</label>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
                                <label for="password" class="text-muted">Password</label>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input shadow-none" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted small" for="remember">
                                        Remember Me
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a class="text-decoration-none small fw-semibold" href="{{ route('password.request') }}">
                                        Forgot Password?
                                    </a>
                                @endif
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-primary btn-lg fw-semibold shadow-sm">
                                     Login
                                </button>
                            </div>
                        </form>

                        <!-- Developer Watermark -->
                        <div class="text-center mt-auto pt-4">
                            <p class="small text-muted mb-0">
                                System Developed by <span class="fw-semibold">Mark Indayon</span>
                            </p>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection