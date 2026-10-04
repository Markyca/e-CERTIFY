@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4 d-print-block">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Print Action Bar (Hidden when printing) -->
            <div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
                <a href="{{ route('settings.edit') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('templates.body', $type) }}" class="btn btn-outline-primary btn-sm px-3" title="Edit certificate body">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    <button onclick="window.print()" class="btn btn-primary btn-sm px-3" title="Print">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                </div>
            </div>

            <!-- A4 Bond Paper Preview Container -->
            <div class="bg-white shadow-lg position-relative mx-auto" style="width: 100%; max-width: 794px; aspect-ratio: 1 / 1.414; padding: 40px 50px; overflow: hidden;">
                
                <style>
                    .header-container {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 20px;
                    }
                    .header-text {
                        font-family: 'Copperplate Gothic Bold', 'Times New Roman', serif;
                        font-size: 10pt;
                        font-weight: bold;
                        line-height: 1.2;
                        text-align: center;
                    }
                    .office-text {
                        font-family: 'Edwardian Script ITC', 'Brush Script MT', cursive;
                        font-size: 20pt;
                        font-weight: bold;
                        text-align: center;
                        margin-top: 3px;
                        margin-bottom: 6px;
                    }
                    .cert-title-container {
                        text-align: center;
                        margin: 10px 0 15px 0;
                    }
                    .cert-main-title {
                        font-family: 'Algerian', 'Times New Roman', serif;
                        font-size: 14pt;
                        font-weight: bold;
                        letter-spacing: 1px;
                        color: #000;
                        margin: 0;
                    }
                    .cert-subtitle {
                        font-family: 'Aptos Display', 'Aptos', Arial, sans-serif;
                        font-size: 10pt;
                        color: #333;
                        margin-top: 2px;
                    }
                    .content-text {
                        font-family: 'Century Gothic', Arial, sans-serif;
                        font-size: 10pt;
                        text-align: justify;
                        line-height: 1.45;
                    }
                    .content-text ol {
                        padding-left: 20px;
                        margin-bottom: 10px;
                    }
                    .content-text li {
                        margin-bottom: 10px;
                    }
                    .signatures-container {
                        display: flex;
                        justify-content: space-between;
                        margin-top: 20px;
                        font-family: 'Century Gothic', Arial, sans-serif;
                        font-size: 11pt;
                    }
                    .signature-box {
                        width: 45%;
                        text-align: center;
                    }
                    .certificate-footer {
                        position: absolute;
                        bottom: 25px;
                        left: 50px;
                        right: 50px;
                        display: flex;
                        align-items: center;
                        gap: 15px;
                        font-family: 'Century Gothic', Arial, sans-serif;
                        font-size: 9pt;
                    }

                    @media print {
                        @page {
                            size: A4;
                            margin: 0;
                        }
                        body {
                            background: white !important;
                        }
                        .container-fluid {
                            padding: 0 !important;
                        }
                        .shadow-lg, .shadow-sm {
                            box-shadow: none !important;
                        }
                        .d-print-none {
                            display: none !important;
                        }
                    }
                
                    /* Editable certificate body */
                    .tpl-body p { text-indent: 45px; margin-bottom: 12px; text-align: justify; }
                    .tpl-body p:last-child { margin-bottom: .5rem; }
                    .tpl-body ul.tpl-list { list-style: none; padding-left: 40px; text-align: justify; margin-bottom: 8px; }
                    .tpl-body ul.tpl-list li { margin-bottom: 2px; }
                </style>

                <!-- Header Section -->
                <div class="header-container">
                    <div style="width: 75px; height: 75px; flex-shrink: 0;">
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

                    <div style="width: 75px; height: 75px; flex-shrink: 0;">
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
                <div class="cert-title-container">
                    <h1 class="cert-main-title">{{ $template->title }}</h1>
                    <div class="cert-subtitle">Republic Act 11261-First Time Job Seekers Assistance Act</div>
                </div>

                <!-- Document Content (9 Declarations) -->
                <div class="content-text">                    <div class="tpl-body">{!! $bodyHtml !!}</div>
                </div>

                <!-- Combined Horizontal Signatures Section (No Underline) -->
                <div style="display: flex; justify-content: space-between; margin-top: 35px; font-family: 'Century Gothic', Arial, sans-serif; font-size: 12pt;">
                    
                    <!-- Left Side: Signed by (Applicant) -->
                    <div style="width: 45%; text-align: center;">
                        <div class="text-start mb-1" style="font-size: 11pt; text-indent: 40px; text-align: justify;">Signed by:</div>
                        <div style="height: 25px;"></div> <!-- Space for physical signature -->
                        <strong class="d-block text-uppercase">[APPLICANT NAME]</strong> <!-- Change placeholder name as needed -->
                        <span style="font-size: 11pt;">First-time Jobseeker</span>
                    </div>

                    <!-- Right Side: Punong Barangay & Witness -->
                    <div style="width: 45%; text-align: center;">
                        <div class="text-start mb-1" style="font-size: 11pt;">Witnessed by:</div>
                        <div style="height: 25px;"></div> <!-- Space for physical signature / breathing room -->
                        <strong class="d-block text-uppercase ">{{ $settings->captain_name }}</strong>
                        <span style="font-size: 11pt;">{{ $settings->captain_title }}</span>
                    </div>

                </div>

                <!-- Footer with QR Code -->
                <div class="certificate-footer">
                    <div style="width: 70px; height: 70px; flex-shrink: 0;">
                        @if($settings->qr_code)
                            <img src="{{ asset('storage/' . $settings->qr_code) }}" alt="QR Code" style="width: 100%; height: 100%; object-fit: contain;">
                        @else
                            <div class="border w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="font-size: 8px;">QR</div>
                        @endif
                    </div>
                    <div>
                        <span class="d-block" style="font-size: 8.5pt; letter-spacing: 0.5px;">{{ $settings->footer_label }}</span>
                        <span class="fw-bold d-block text-muted" style="font-size: 8.5pt;">{{ $settings->footer_address }}</span>
                        @if($settings->footer_email)<span class="text-primary" style="font-size: 8.5pt;">{{ $settings->footer_email }}</span>@endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection