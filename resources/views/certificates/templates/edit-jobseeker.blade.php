@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    
    <!-- @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif -->

    <div class="row h-100">
        <!-- LEFT COLUMN: The Settings Form -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-primary mb-0">Edit Template</h5>
                    <p class="text-muted small">Update the settings for First Time Job Seeker</p>
                </div>
                <div class="card-body px-4">
                    
                    <div class="alert alert-warning border-0 shadow-sm" style="background-color: #fff3cd;">
                        <i class="bi bi-shield-lock-fill me-2"></i>
                        <strong>Statutory Form:</strong> The body content and Oath of Undertaking for this certificate are dictated by RA 11261 and are locked to ensure legal compliance.
                    </div>

                    <form action="{{ route('templates.update', $template->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Hidden fields to pass validation since they can't edit these here -->
                        <input type="hidden" name="title" value="{{ $template->title }}">
                        <input type="hidden" name="salutation" value="{{ $template->salutation }}">
                        <input type="hidden" name="body_content" value="{{ $template->body_content }}">

                        <div class="row mb-3 mt-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Signatory Name</label>
                                <input type="text" name="signatory_name" id="input_sig_name" class="form-control" value="{{ $template->signatory_name }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Signatory Title</label>
                                <input type="text" name="signatory_title" id="input_sig_title" class="form-control" value="{{ $template->signatory_title }}">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="fw-bold text-dark">Official Images</h6>
                        <p class="small text-muted mb-3">Uploading images here will automatically sync them to all other certificates.</p>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">LGU Logo</label>
                                <input type="file" name="lgu_logo" class="form-control form-control-sm" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Barangay Logo</label>
                                <input type="file" name="brgy_logo" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Captain's Signature</label>
                            <input type="file" name="captain_signature" class="form-control form-control-sm" accept="image/*">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">Save & Sync Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: The 2-Page Preview -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 bg-light p-4 d-flex flex-column align-items-center gap-4 overflow-auto" style="height: 85vh;">
                
                <style>
                    .header-text { font-family: Arial, sans-serif; font-size: 11pt; }
                    .office-text { font-family: "Monotype Corsiva", "Brush Script MT", cursive; font-size: 18pt; margin-top: 10px; }
                    .content-text { font-size: 12pt; text-align: justify; line-height: 1.6; margin-top: 30px; }
                    .numbered-list { margin-left: 20px; text-align: justify; font-size: 11pt; }
                    .numbered-list li { margin-bottom: 8px; }
                </style>

                <!-- PAGE 1 PREVIEW -->
                <div class="bg-white shadow-sm p-5" style="width: 100%; max-width: 800px; aspect-ratio: 8.5/11; position: relative;">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div style="width: 80px; height: 80px;">
                            @if($template->lgu_logo)
                                <img src="{{ asset('storage/' . $template->lgu_logo) }}" alt="LGU" style="width: 100%; height: 100%; object-fit: contain;">
                            @else
                                <div style="width: 100%; height: 100%; border-radius: 50%; background-color: #ddd; text-align: center; line-height: 80px; font-size: 10px;">LGU Logo</div>
                            @endif
                        </div>
                        <div class="text-center header-text lh-1">
                            <span class="d-block">Republic of the Philippines</span>
                            <span class="d-block">Province of Isabela</span>
                            <span class="d-block">Municipality of Mallig</span>
                            <span class="d-block">BARANGAY OF SIEMPRE VIVA SUR</span>
                        </div>
                        <div style="width: 80px; height: 80px;">
                            @if($template->brgy_logo)
                                <img src="{{ asset('storage/' . $template->brgy_logo) }}" alt="Brgy" style="width: 100%; height: 100%; object-fit: contain;">
                            @else
                                <div style="width: 100%; height: 100%; border-radius: 50%; background-color: #ddd; text-align: center; line-height: 80px; font-size: 10px;">Brgy Logo</div>
                            @endif
                        </div>
                    </div>
                    <hr class="border-1 border-dark my-1">
                    
                    <div class="text-center mt-4 mb-4">
                        <h4 class="fw-bold mb-0" style="font-family: 'Times New Roman', serif;">OFFICE OF THE PUNONG BARANGAY</h4>
                    </div>
                    <div class="text-center mb-5">
                        <h2 class="fw-bold mb-0" style="font-family: 'Times New Roman', serif; font-size: 22pt;">BARANGAY CERTIFICATION</h2>
                        <p style="font-size: 11pt;">(First-Time Job Seekers Assistance Act-RA 11261)</p>
                    </div>

                    <div class="content-text">
                        <p class="fw-bold mb-4">TO WHOM IT MAY CONCERN:</p>
                        <p style="text-indent: 40px;">
                            THIS IS TO CERTIFY that <strong>MR. JUAN M. DELA CRUZ</strong>, a resident of Barangay Siempre Viva Sur, Mallig, Isabela, is a qualified applicant under RA 11261, known as the First-Time Job Seekers Act of 2019.
                        </p>
                        <p style="text-indent: 40px;">
                            I further certify that the bearer was informed of his rights, including the duties and responsibilities accorded by RA 11261, through the Oath of Undertaking he has signed and executed in the presence of our Barangay Official.
                        </p>
                    </div>

                    <div class="row mt-5">
                        <div class="col-6"></div>
                        <div class="col-6 text-center">
                            @if($template->captain_signature)
                                <div style="margin-bottom: -30px; position: relative; z-index: 10;">
                                    <img src="{{ asset('storage/' . $template->captain_signature) }}" alt="Signature" style="height: 60px; object-fit: contain;">
                                </div>
                            @endif
                            <strong class="d-block" style="font-size: 12pt; position: relative; z-index: 11;" id="preview_sig_name_1">{{ $template->signatory_name }}</strong>
                            <span style="font-size: 11pt;" id="preview_sig_title_1">{{ $template->signatory_title }}</span><br>
                        </div>
                    </div>
                </div>

                <!-- PAGE 2 PREVIEW -->
                <div class="bg-white shadow-sm p-5" style="width: 100%; max-width: 800px; aspect-ratio: 8.5/11; position: relative;">
                    <div class="text-center mt-3 mb-4">
                        <h3 class="fw-bold fst-italic mb-0" style="font-family: 'Times New Roman', serif;">OATH OF UNDERTAKING</h3>
                        <p style="font-size: 10pt;">Republic Act 11261-First Time Job Seekers Assistance Act</p>
                    </div>
                    <div class="content-text" style="font-size: 11pt;">
                        <p style="text-indent: 40px;">
                            I, <strong>JUAN M. DELA CRUZ</strong>, 25 years of age, resident of barangay Siempre Viva Sur...
                        </p>
                        <ol class="numbered-list text-muted">
                            <li>That this is the first time that I will actively take a job...</li>
                            <li>That I am aware that the benefit and privilege/s...</li>
                            <li>[Remaining clauses hidden for preview simplicity...]</li>
                        </ol>
                    </div>
                    <div class="row mt-5">
                        <div class="col-6 text-center">
                            <span class="d-block mb-4 text-start ps-4">Signed by:</span>
                            <strong class="d-block">JUAN M. DELA CRUZ</strong>
                        </div>
                        <div class="col-6 text-center">
                            <span class="d-block mb-4 text-start ps-4">Witnessed by:</span>
                            <strong class="d-block" id="preview_sig_name_2">{{ $template->signatory_name }}</strong>
                            <span style="font-size: 11pt;" id="preview_sig_title_2">{{ $template->signatory_title }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('input_sig_name');
        const titleInput = document.getElementById('input_sig_title');

        nameInput.addEventListener('input', () => {
            document.getElementById('preview_sig_name_1').innerText = nameInput.value;
            document.getElementById('preview_sig_name_2').innerText = nameInput.value;
        });

        titleInput.addEventListener('input', () => {
            document.getElementById('preview_sig_title_1').innerText = titleInput.value;
            document.getElementById('preview_sig_title_2').innerText = titleInput.value;
        });
    });
</script>
@endsection