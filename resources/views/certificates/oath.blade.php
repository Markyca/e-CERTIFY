@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4 d-print-block">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <!-- Print Action Bar (Hidden when printing) -->
            <div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
                <a href="{{ route('settings.edit') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Global Settings
                </a>
                <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
                    <i class="bi bi-printer me-1"></i> Print Certificate
                </button>
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
                        <span>REPUBLIC OF THE PHILIPPINES</span><br>
                        <span>PROVINCE OF ISABELA</span><br>
                        <span>MUNICIPALITY OF MALLIG</span><br>
                        <span>BARANGAY OF SIEMPRE VIVA SUR</span>
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
                <div class="office-text">Office of the Punong Barangay</div>

                <!-- Oath Title -->
                <div class="cert-title-container">
                    <h1 class="cert-main-title">OATH OF UNDERTAKING</h1>
                    <div class="cert-subtitle">Republic Act 11261-First Time Job Seekers Assistance Act</div>
                </div>

                <!-- Document Content (9 Declarations) -->
                <div class="content-text">
                    <p class="mb-2" style="text-indent: 40px; text-align: justify;">
                        <strong>I, [SAMPLE APPLICANT NAME]</strong>, [AGE] years of age, resident of Barangay Siempre Viva Sur, Mallig, Isabela, availing the benefit of Republic Act <strong>11261</strong>, otherwise known as the <strong>First Time Jobseekers Act of 2019</strong>, agree and undertake to abide and be bound by the following;
                    </p>

                    <ul style="list-style: none; padding-left: 40px; text-align: justify;">
                        <li><strong>1.</strong> That is the first time that I will actively take a job and therefore request that the Barangay Certification be issued in my favor to avail the benefit of the law;</li>
                        <li><strong>2.</strong> That I am aware that the benefit and privilege/s under the said law shall be valid only for one (1) year from the date that the Barangay Certification is issued;</li>
                        <li><strong>3.</strong> That I can avail myself of the benefits of the law only once;</li>
                        <li><strong>4.</strong> That I understand that my personal information shall be included in the Roster/List of the First Time Jobseekers and not be used for any unlawful purpose; means, or through my family/relatives once I get employed;</li>
                        <li><strong>5.</strong> That I will inform and/or report to the Barangay personally, through text or other options, that I am not a beneficiary of the Job Start Program under RA No. 10869 and other laws that give similar exemptions for the documents and other transactions exempted under RA No. 11261;</li>
                        <li><strong>6.</strong> That if issued the requested Certification, I will not use the same in any fraud, nor falsify nor help and/or assist in the fabrication of the said certification;</li>
                        <li><strong>7.</strong> That this undertaking is made solely for the purpose of obtaining a barangay Certification consistent with the objective of RA No. 11261, and/or not for any other purpose;</li>
                        <li><strong>8.</strong> That I consent to the use of my personal information pursuant to the Data Privacy Act and other applicable laws, rules, and regulations; and</li>
                        <li><strong>9.</strong> That I consent to the use of my personal information pursuant to the Data Privacy Act and other applicable laws, rules, and regulations.</li>
                    </ul>

                    <p class="mt-2" style="text-indent: 40px; text-align: justify;">
                        Signed this [DAY] day of [MONTH] [YEAR], at Barangay Siempre Viva Sur, Mallig, Isabela.
                    </p>
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
                        <span class="d-block" style="font-size: 8.5pt; letter-spacing: 0.5px;">SCAN FOR MORE INFO. OR EMAIL</span>
                        <span class="fw-bold d-block text-muted" style="font-size: 8.5pt;">Brgy. Siempre Viva Sur, Mallig, Isabela</span>
                        <span class="text-primary" style="font-size: 8.5pt;">siemprevivasurbarangay@gmail.com</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection