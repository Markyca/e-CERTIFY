@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    
    <div class="row g-4">
        <!-- LEFT COLUMN: The Settings Form (Text & Content Only) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-primary mb-0">Edit Template</h5>
                    <p class="text-muted small">Update the content format for {{ ucfirst($type ?? $template->title) }}</p>
                </div>
                <div class="card-body px-4">
                    <form action="{{ route('templates.update', $template->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Document Title</label>
                            <input type="text" name="title" id="input_title" class="form-control" value="{{ $template->title }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Salutation</label>
                            <input type="text" name="salutation" id="input_salutation" class="form-control" value="{{ $template->salutation }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Body Content</label>
                            <textarea name="body_content" id="input_body" class="form-control" rows="8">{{ $template->body_content }}</textarea>
                            <div class="form-text small text-muted mt-2">
                                <strong>Available Shortcodes:</strong><br>
                                {first_name}, {last_name}, {middle_initial}, {age}, {gender}, {civil_status}, {purok}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Signatory Name</label>
                                <input type="text" name="signatory_name" id="input_sig_name" class="form-control" value="{{ $template->signatory_name }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Signatory Title</label>
                                <input type="text" name="signatory_title" id="input_sig_title" class="form-control" value="{{ $template->signatory_title }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">Save Template Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: The Live Print Preview (Bond Paper Style) -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 bg-light p-4 d-flex justify-content-center align-items-center" style="min-height: 85vh;">
                
                <!-- 8.5 x 11 Bond Paper Container -->
                <div class="bg-white shadow-lg p-5 position-relative" style="width: 100%; max-width: 800px; aspect-ratio: 8.5/11; overflow: hidden;">
                    
                    <style>
                        /* Exact Official Typography & Layout Rules */
                        .header-text {
                            font-family: 'Copperplate Gothic Bold', 'Times New Roman', serif;
                            font-size: 11pt;
                            font-weight: bold;
                            line-height: 1.3;
                        }
                        .office-text {
                            font-family: 'Edwardian Script ITC', 'Brush Script MT', cursive;
                            font-size: 24pt;
                            font-weight: bold;
                            text-align: center;
                            margin-top: 5px;
                            margin-bottom: 10px;
                        }
                        .ribbon-container {
                            text-align: center;
                            position: relative;
                            width: 85%;
                            margin: 10px auto 20px auto;
                        }
                        .ribbon-banner {
                            width: 100%;
                            height: auto;
                            object-fit: contain;
                        }
                        .ribbon-title {
                            position: absolute;
                            top: 50%;
                            left: 50%;
                            transform: translate(-50%, -50%);
                            font-family: 'Algerian', 'Times New Roman', serif;
                            font-size: 22pt;
                            font-weight: bold;
                            letter-spacing: 1px;
                            color: #000;
                            text-transform: uppercase;
                            width: 100%;
                            margin: 0;
                        }
                        .content-text {
                            font-family: 'Century Gothic', Arial, sans-serif;
                            font-size: 13pt;
                            text-align: justify;
                            line-height: 1.6;
                        }
                        .content-text p {
                            text-indent: 50px;
                            margin-bottom: 12px;
                        }
                        .signatory {
                            margin-top: 35px;
                            float: right;
                            text-align: center;
                            font-family: 'Century Gothic', Arial, sans-serif;
                            font-size: 13pt;
                        }
                        .certificate-footer {
                            position: absolute;
                            bottom: 30px;
                            left: 48px;
                            right: 48px;
                            display: flex;
                            align-items: center;
                            gap: 15px;
                            font-family: 'Century Gothic', Arial, sans-serif;
                            font-size: 9pt;
                        }
                    </style>

                    <!-- Header Section with Static Logos -->
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="width: 75px; height: 75px;">
                            <img src="{{ asset('images/lgu-logo.jpg') }}" alt="LGU Logo" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        
                        <div class="text-center header-text">
                            <span>REPUBLIC OF THE PHILIPPINES</span><br>
                            <span>PROVINCE OF ISABELA</span><br>
                            <span>MUNICIPALITY OF MALLIG</span><br>
                            <span>BARANGAY OF SIEMPRE VIVA SUR</span>
                        </div>

                        <div style="width: 75px; height: 75px;">
                            <img src="{{ asset('images/brgy-logo.jpg') }}" alt="Brgy Logo" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                    </div>

                    <hr class="border-1 border-dark my-1">
                    <div class="office-text">Office of the Punong Barangay</div>

                    <!-- Static Ribbon Banner with Dynamic Title -->
                    <div class="ribbon-container">
                        <img src="{{ asset('images/ribbon.jpg') }}" alt="Ribbon Banner" class="ribbon-banner">
                        <h1 class="ribbon-title" id="preview_title">{{ $template->title }}</h1>
                    </div>

                    <!-- Document Content -->
                    <div class="content-text">
                        <p class="fw-bold mb-3" style="text-indent: 0 !important;" id="preview_salutation">{{ $template->salutation }}</p>
                        <div id="preview_body">
                            <!-- Dynamic body preview injected via JS -->
                        </div>
                    </div>

                    <!-- Signatory Block -->
                    <div class="signatory">
                        <div class="text-center">
                            <strong class="d-block text-uppercase" id="preview_sig_name">{{ $template->signatory_name }}</strong>
                            <span style="font-size: 12pt;" id="preview_sig_title">{{ $template->signatory_title }}</span>
                        </div>
                    </div>

                    <!-- Static Footer with QR Code & Contact Info -->
                    <div class="certificate-footer">
                        <div style="width: 60px; height: 60px;">
                            <img src="{{ asset('images/qr-code.jpg') }}" alt="QR Code" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <div>
                            <span class="fw-bold d-block" style="font-size: 8pt; letter-spacing: 0.5px;">SCAN FOR MORE INFO. OR EMAIL</span>
                            <span class="d-block text-muted">Brgy. Siempre Viva Sur, Mallig, Isabela</span>
                            <span class="text-primary">siemprevivasurbarangay@gmail.com</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- LIVE PREVIEW JAVASCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dummyData = {
            '{first_name}': 'KING JAMES',
            '{last_name}': 'SALDIVAR',
            '{middle_initial}': 'P.',
            '{age}': '16',
            '{gender}': 'male',
            '{civil_status}': 'single',
            '{purok}': '1'
        };

        const bodyInput = document.getElementById('input_body');
        const bodyPreview = document.getElementById('preview_body');

        function updateBodyPreview() {
            let text = bodyInput.value;
            
            for (const [code, value] of Object.entries(dummyData)) {
                text = text.replaceAll(code, `<strong>${value}</strong>`);
            }

            const paragraphs = text.split('\n').filter(p => p.trim() !== '');
            bodyPreview.innerHTML = paragraphs.map(p => `<p>${p}</p>`).join('');
        }

        function bindInput(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            
            input.addEventListener('input', () => {
                preview.innerText = input.value;
            });
        }

        bodyInput.addEventListener('input', updateBodyPreview);
        bindInput('input_title', 'preview_title');
        bindInput('input_salutation', 'preview_salutation');
        bindInput('input_sig_name', 'preview_sig_name');
        bindInput('input_sig_title', 'preview_sig_title');

        updateBodyPreview();
    });
</script>
@endsection