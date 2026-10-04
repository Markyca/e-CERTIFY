@extends('layouts.app')

@section('content')

<div class="container-fluid px-4 py-4 d-print-block">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Print Action Bar (Hidden when printing) -->
            <div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
                <a href="{{ route('residents.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Residents
                </a>
                <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
                    <i class="bi bi-printer me-1"></i> Print 2-Page Job Seeker Set
                </button>
            </div>

            <style>
                .a4-page {
                    background: white;
                    width: 100%;
                    max-width: 794px;
                    aspect-ratio: 1 / 1.414;
                    position: relative;
                    margin: 0 auto 40px auto;
                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                    overflow: hidden;
                    page-break-after: always;
                    padding: 45px 55px;
                }
                .header-container { display: flex; align-items: center; justify-content: center; gap: 20px; }
                .header-text { font-family: 'Copperplate Gothic Bold', serif; font-size: 11pt; font-weight: bold; line-height: 1.2; text-align: center; }
                .office-text { font-family: 'Edwardian Script ITC', cursive; font-size: 22pt; font-weight: bold; text-align: center; margin-top: 5px; margin-bottom: 8px; }
                .cert-main-title { font-family: 'Algerian', serif; font-size: 22pt; font-weight: bold; text-align: center; margin: 0; }
                .cert-subtitle { font-family: 'Aptos Display', 'Aptos', sans-serif; font-size: 11pt; text-align: center; color: #333; margin-top: 2px; }
                .content-text { font-family: 'Century Gothic', Arial, sans-serif; font-size: 13pt; text-align: justify; line-height: 1.6; }
                .content-text p { text-indent: 45px; margin-bottom: 15px; }
                .certificate-footer { position: absolute; bottom: 30px; left: 55px; right: 55px; display: flex; align-items: center; gap: 15px; font-family: 'Century Gothic', Arial, sans-serif; font-size: 9pt; }

                @media print {
                    @page { size: A4; margin: 0; }
                    body { background: white !important; }
                    .container-fluid { padding: 0 !important; }
                    .a4-page { box-shadow: none !important; margin: 0 !important; page-break-after: always; }
                    .d-print-none { display: none !important; }
                }
            
                    /* Editable certificate body */
                    .tpl-body p { text-indent: 45px; margin-bottom: 12px; text-align: justify; }
                    .tpl-body p:last-child { margin-bottom: 4rem; }
                    .tpl-body ul.tpl-list { list-style: none; padding-left: 40px; text-align: justify; margin-bottom: 8px; }
                    .tpl-body ul.tpl-list li { margin-bottom: 2px; }
                    .tpl-cert p:last-child { margin-bottom: 1.5rem; }
                    .tpl-oath p:last-child { margin-bottom: .5rem; }
                    .tpl-oath p { text-indent: 40px; margin-bottom: 8px; }
                </style>

            <!-- ================= PAGE 1: FIRST-TIME JOB SEEKER CERTIFICATION ================= -->
            <div class="a4-page">
                <!-- Header Section -->
                <div class="header-container">
                    <div style="width: 85px; height: 85px; flex-shrink: 0;">
                        @if($settings->lgu_logo)
                            <img src="{{ asset('storage/' . $settings->lgu_logo) }}" alt="LGU Logo" style="width: 100%; height: 100%; object-fit: contain;">
                        @else
                            <div class="border rounded-circle w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="font-size: 8px;">LGU</div>
                        @endif
                    </div>
                    
                    <div class="header-text">
                        @foreach($settings->header_lines_list as $line)
                        <span>{{ $line }}</span>@if(!$loop->last)<br>@endif
                        @endforeach
                    </div>

                    <div style="width: 85px; height: 85px; flex-shrink: 0;">
                        @if($settings->brgy_logo)
                            <img src="{{ asset('storage/' . $settings->brgy_logo) }}" alt="Brgy Logo" style="width: 100%; height: 100%; object-fit: contain;">
                        @else
                            <div class="border rounded-circle w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="font-size: 8px;">Brgy</div>
                        @endif
                    </div>
                </div>

                <hr style="border-top: 2px solid #000; opacity: 1;" class="my-1">
                <div class="office-text">{{ $settings->header_office }}</div>

                <!-- Job Seeker Title -->
                <div style="text-align: center; margin: 15px 0 25px 0;">
                    <h1 class="cert-main-title">{{ $certTemplate->title }}</h1>
                    <div class="cert-subtitle">(First-Time Jobseekers Assistance Act - RA 11261)</div>
                </div>

                <!-- Document Content -->
                <div class="content-text">
                    @if(!empty($certTemplate->salutation))
                    <p class="fw-bold mb-3" style="text-indent: 0 !important;">{{ $certTemplate->salutation }}</p>
                    @endif
                    <div class="tpl-body tpl-cert">{!! $certBodyHtml !!}</div>
                </div>

                <!-- Vertical Signatory Blocks for Page 1 -->
                <div style="margin-top: 25px; float: right; text-align: center; font-family: 'Century Gothic', Arial, sans-serif; font-size: 12pt; min-width: 280px;">
                    <div class="text-center">
                        <strong class="d-block text-uppercase">{{ $settings->captain_name }}</strong>
                        <span style="font-size: 12pt;">{{ $settings->captain_title }}</span>
                        <span class="d-block" style="font-size: 12pt;">{{ date('F j, Y') }}</span>
                    </div>
                </div>

                <div style="margin-top: 20px; float: right; text-align: center; font-family: 'Century Gothic', Arial, sans-serif; font-size: 12pt; min-width: 280px; clear: both;">
                    <div class="text-start mb-1" style="text-align: left !important; font-size: 12pt;">Witnessed by:</div>
                    <div class="text-center mt-2">
                        <strong class="d-block text-uppercase" style="margin-top: 30px;">{{ strtoupper($witnessName) }}</strong>
                        <span style="font-size: 12pt;">{{ $witnessTitle }}</span>
                        <span class="d-block" style="font-size: 12pt;">{{ date('F j, Y') }}</span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="certificate-footer">
                    <div style="width: 100px; height: 100px; flex-shrink: 0;">
                        @if($settings->qr_code)
                            <img src="{{ asset('storage/' . $settings->qr_code) }}" alt="QR Code" style="width: 100%; height: 100%; object-fit: contain;">
                        @else
                            <div class="border w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="font-size: 8px;">QR</div>
                        @endif
                    </div>
                    <div>
                        <span class="d-block" style="font-size: 9pt; letter-spacing: 0.5px;">{{ $settings->footer_label }}</span>
                        <span class="fw-bold d-block text-muted" style="font-size: 9pt;">{{ $settings->footer_address }}</span>
                        @if($settings->footer_email)<span class="text-primary" style="font-size: 9pt;">{{ $settings->footer_email }}</span>@endif
                    </div>
                </div>
            </div>


            <!-- ================= PAGE 2: OATH OF UNDERTAKING ================= -->
            <div class="a4-page">
                <!-- Header Section -->
                <div class="header-container">
                    <div style="width: 85px; height: 85px; flex-shrink: 0;">
                        @if($settings->lgu_logo)
                            <img src="{{ asset('storage/' . $settings->lgu_logo) }}" alt="LGU Logo" style="width: 100%; height: 100%; object-fit: contain;">
                        @else
                            <div class="border rounded-circle w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="font-size: 8px;">LGU</div>
                        @endif
                    </div>
                    
                    <div class="header-text">
                        @foreach($settings->header_lines_list as $line)
                        <span>{{ $line }}</span>@if(!$loop->last)<br>@endif
                        @endforeach
                    </div>

                    <div style="width: 85px; height: 85px; flex-shrink: 0;">
                        @if($settings->brgy_logo)
                            <img src="{{ asset('storage/' . $settings->brgy_logo) }}" alt="Brgy Logo" style="width: 100%; height: 100%; object-fit: contain;">
                        @else
                            <div class="border rounded-circle w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="font-size: 8px;">Brgy</div>
                        @endif
                    </div>
                </div>

                <hr style="border-top: 2px solid #000; opacity: 1;" class="my-1">
                <div class="office-text">{{ $settings->header_office }}</div>

                <!-- Oath Title -->
                <div style="text-align: center; margin: 10px 0 15px 0;">
                    <h1 style="font-family: 'Algerian', serif; font-size: 14pt; font-weight: bold; letter-spacing: 1px; margin: 0;">{{ $oathTemplate->title }}</h1>
                    <div style="font-family: 'Aptos Display', sans-serif; font-size: 10pt; color: #333; margin-top: 2px;">Republic Act 11261 - First Time Job Seekers Assistance Act</div>
                </div>

                <!-- Document Content (9 Declarations) -->
                <div style="font-family: 'Century Gothic', Arial, sans-serif; font-size: 10pt; text-align: justify; line-height: 1.45;">
                    <div class="tpl-body tpl-oath">{!! $oathBodyHtml !!}</div>
                </div>

                <!-- Horizontal Signatures Section for Page 2 (Signed by Applicant + Witnessed by Captain) -->
                <div style="display: flex; justify-content: space-between; margin-top: 25px; font-family: 'Century Gothic', Arial, sans-serif; font-size: 11pt;">
                    <!-- Left Side: Signed by Applicant -->
                    <div style="width: 45%; text-align: center;">
                        <div class="text-start mb-1" style="font-size: 11pt; text-indent: 40px;">Signed by:</div>
                        <div style="height: 30px;"></div>
                        <strong class="d-block text-uppercase" style="font-size: 12pt;">{{ $standardFullName }}</strong>
                        <span style="font-size: 12pt;">First-time Jobseeker</span>
                    </div>

                    <!-- Right Side: Witnessed by Captain -->
                    <div style="width: 45%; text-align: center;">
                        <div class="text-start mb-1" style="font-size: 11pt;">Witnessed by:</div>
                        <div style="height: 30px;"></div>
                        <strong class="d-block text-uppercase" style="font-size: 12pt;">{{ $settings->captain_name }}</strong>
                        <span style="font-size: 12pt;">{{ $settings->captain_title }}</span>
                    </div>
                </div>

                <!-- Footer -->
                <div class="certificate-footer">
                    <div style="width: 100px; height: 100px; flex-shrink: 0;">
                        @if($settings->qr_code)
                            <img src="{{ asset('storage/' . $settings->qr_code) }}" alt="QR Code" style="width: 100%; height: 100%; object-fit: contain;">
                        @else
                            <div class="border w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="font-size: 8px;">QR</div>
                        @endif
                    </div>
                    <div>
                        <span class="d-block" style="font-size: 9pt; letter-spacing: 0.5px;">{{ $settings->footer_label }}</span>
                        <span class="fw-bold d-block text-muted" style="font-size: 9pt;">{{ $settings->footer_address }}</span>
                        @if($settings->footer_email)<span class="text-primary" style="font-size: 9pt;">{{ $settings->footer_email }}</span>@endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection