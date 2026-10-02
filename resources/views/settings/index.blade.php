@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h4 class="fw-bold text-dark mb-1">Barangay System Settings</h4>
                <p class="text-muted small mb-4">Manage global municipal assets, official signatories, and signature preferences used across all certificate templates.</p>


                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Official Signatory Details -->
                    <h6 class="fw-bold text-primary mb-3">Punong Barangay Signatory Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Captain's Full Name</label>
                            <input type="text" name="captain_name" class="form-control" value="{{ old('captain_name', $settings->captain_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Official Title</label>
                            <input type="text" name="captain_title" class="form-control" value="{{ old('captain_title', $settings->captain_title) }}" required>
                        </div>
                    </div>

                    <!-- Digital Signature Toggle -->
                    <div class="mb-4 p-3 bg-light rounded-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="showSignatureSwitch" name="show_signature" value="1" {{ $settings->show_signature ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-dark ms-2" for="showSignatureSwitch">
                                Enable Digital Signature on Printed Certificates
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1 ms-4">When toggled off, the signature image will be hidden during printing, leaving space for a physical wet signature.</small>
                    </div>

                    <!-- Static Image Assets Uploads -->
                    <h6 class="fw-bold text-primary mb-3">Global Official Image Assets</h6>
                    <div class="row g-4 mb-4">
                        <!-- LGU Logo -->
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">LGU Municipality Logo</label>
                            @if($settings->lgu_logo)
                                <div class="mb-2"><img src="{{ asset('storage/' . $settings->lgu_logo) }}" alt="LGU Logo" style="height: 50px; object-fit: contain;"></div>
                            @endif
                            <input type="file" name="lgu_logo" class="form-control form-control-sm" accept="image/*">
                        </div>

                        <!-- Barangay Logo -->
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Barangay Official Logo</label>
                            @if($settings->brgy_logo)
                                <div class="mb-2"><img src="{{ asset('storage/' . $settings->brgy_logo) }}" alt="Brgy Logo" style="height: 50px; object-fit: contain;"></div>
                            @endif
                            <input type="file" name="brgy_logo" class="form-control form-control-sm" accept="image/*">
                        </div>

                        <!-- QR Code -->
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Footer QR Code</label>
                            @if($settings->qr_code)
                                <div class="mb-2"><img src="{{ asset('storage/' . $settings->qr_code) }}" alt="QR Code" style="height: 50px; object-fit: contain;"></div>
                            @endif
                            <input type="file" name="qr_code" class="form-control form-control-sm" accept="image/*">
                        </div>

                        <!-- Captain Signature -->
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Punong Barangay Signature (Transparent PNG)</label>
                            @if($settings->captain_signature)
                                <div class="mb-2"><img src="{{ asset('storage/' . $settings->captain_signature) }}" alt="Signature" style="height: 50px; object-fit: contain;"></div>
                            @endif
                            <input type="file" name="captain_signature" class="form-control form-control-sm" accept="image/*">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4 py-2">Save Global Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection