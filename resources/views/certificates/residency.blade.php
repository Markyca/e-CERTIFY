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
            <div class="bg-white shadow-lg position-relative mx-auto" style="width: 100%; max-width: 794px; aspect-ratio: 1 / 1.414; padding: 45px 55px; overflow: hidden;">
                
                <style>
                    /* Exact Official Typography & Layout Rules */
                    .header-container {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 30px;
                    }
                    .header-text {
                        font-family: 'Copperplate Gothic Bold', 'Times New Roman', serif;
                        font-size: 11pt;
                        font-weight: bold;
                        line-height: 1.2;
                        text-align: center;
                    }
                    .office-text {
                        font-family: 'Edwardian Script ITC', 'Brush Script MT', cursive;
                        font-size: 22pt;
                        font-weight: bold;
                        text-align: center;
                        margin-top: 5px;
                        margin-bottom: 8px;
                    }
                    .ribbon-container {
                        text-align: center;
                        position: relative;
                        width: 95%;
                        margin: 5px auto 15px auto;
                    }
                    .ribbon-banner {
                        width: 100%;
                        height: auto;
                        object-fit: contain;
                    }
                    .ribbon-title {
                        position: absolute;
                        top: 52%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        font-family: 'Algerian', 'Times New Roman', serif;
                        font-size: 20pt;
                        font-weight: bold;
                        letter-spacing: 1px;
                        color: #000;
                        text-transform: uppercase;
                        width: 100%;
                        margin: 0;
                        white-space: nowrap;
                    }
                    .content-text {
                        font-family: 'Century Gothic', Arial, sans-serif;
                        font-size: 13pt;
                        text-align: justify;
                        line-height: 1.6;
                    }
                    .content-text p {
                        text-indent: 45px;
                        margin-bottom: 12px;
                    }
                    .signatory {
                        margin-top: 45px;
                        float: right;
                        text-align: center;
                        font-family: 'Century Gothic', Arial, sans-serif;
                        font-size: 13pt;
                    }
                    .certificate-footer {
                        position: absolute;
                        bottom: 30px;
                        left: 55px;
                        right: 55px;
                        display: flex;
                        align-items: center;
                        gap: 15px;
                        font-family: 'Century Gothic', Arial, sans-serif;
                        font-size: 9pt;
                    }

                    /* Print Optimization for A4 */
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
                    .tpl-body p:last-child { margin-bottom: 4rem; }
                    .tpl-body ul.tpl-list { list-style: none; padding-left: 40px; text-align: justify; margin-bottom: 8px; }
                    .tpl-body ul.tpl-list li { margin-bottom: 2px; }
                </style>

                <!-- Header Section with Tighter Centered Spacing -->
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
                <div class="office-text" style="margin-bottom: 2rem;">{{ $settings->header_office }}</div>

                <!-- Ribbon Banner & Algerian Title -->
                <div class="ribbon-container" style="margin-bottom: 3rem;">
                    <img src="{{ asset('images/ribbon.jpg') }}" alt="Ribbon Banner" class="ribbon-banner">
                    <h1 class="ribbon-title">{{ $template->title }}</h1>
                </div>

                <!-- Document Content (Sample Resident Blueprint) -->
                <div class="content-text">
                    @if(!empty($template->salutation))
                    <p class="fw-bold mb-3" style="text-indent: 0 !important;">{{ $template->salutation }}</p>
                    @endif
                    <div class="tpl-body">{!! $bodyHtml !!}</div>
                </div>

                <!-- Scaled Up Signatory Block & Digital Signature -->
                <div class="signatory">
                    <div class="text-center position-relative" style="min-width: 250px;">
                        @if($settings->show_signature && $settings->captain_signature)
                            <div style="position: absolute; top: -60px; left: 50%; transform: translateX(-50%); z-index: 5; pointer-events: none;">
                                <img src="{{ asset('storage/' . $settings->captain_signature) }}" alt="Captain Signature" style="height: 105px; width: auto; object-fit: contain;">
                            </div>
                        @endif
                        
                        <strong class="d-block text-uppercase " style="position: relative; z-index: 6;">{{ $settings->captain_name }}</strong>
                        <span style="font-size: 12pt; position: relative; z-index: 6;">{{ $settings->captain_title }}</span>
                    </div>
                </div>

                <!-- Scaled Up Static Footer with QR Code & Contact Info -->
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