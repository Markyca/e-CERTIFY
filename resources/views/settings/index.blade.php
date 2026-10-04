@extends('layouts.app')

@section('content')
<style>
    .section-title { display: flex; align-items: center; gap: .6rem; font-weight: 700; color: var(--ec-ink); margin-bottom: 1.1rem; }
    .section-title .dot { width: 34px; height: 34px; border-radius: .7rem; display: grid; place-items: center; background: var(--ec-100); color: var(--ec-600); }
    .asset-box { border: 1.5px dashed var(--ec-200); border-radius: 1rem; padding: .9rem; background: var(--ec-50); height: 100%; }
    .asset-box img { height: 54px; object-fit: contain; background: #fff; border-radius: .6rem; padding: 4px; border: 1px solid var(--ec-line); }
    /* Paper-style live preview of the global header / footer */
    .paper-preview { background: #fff; border: 1px solid var(--ec-line); border-radius: 1rem; padding: 1.1rem 1.3rem; box-shadow: 0 10px 30px -22px rgba(30, 64, 175, .6); }
    .pv-header { display: flex; align-items: center; justify-content: center; gap: 22px; }
    .pv-logo { width: 62px; height: 62px; flex: 0 0 62px; }
    .pv-logo img { width: 100%; height: 100%; object-fit: contain; }
    .pv-logo .ph { width: 100%; height: 100%; border: 1px dashed #cbd5e1; border-radius: 50%; display: grid; place-items: center; font-size: 9px; color: #94a3b8; }
    .pv-lines { font-family: 'Copperplate Gothic Bold', 'Times New Roman', serif; font-weight: bold; font-size: 9.5pt; line-height: 1.25; text-align: center; color: #000; }
    .pv-office { font-family: 'Edwardian Script ITC', 'Brush Script MT', cursive; font-size: 19pt; font-weight: bold; text-align: center; color: #000; margin-top: 2px; }
    .pv-rule { border: 0; border-top: 2px solid #000; opacity: 1; margin: 6px 0 0; }
    .pv-footer { display: flex; align-items: center; gap: 14px; font-family: 'Century Gothic', Arial, sans-serif; font-size: 8.5pt; }
    .pv-qr { width: 64px; height: 64px; flex: 0 0 64px; }
    .pv-qr img { width: 100%; height: 100%; object-fit: contain; }
    .pv-qr .ph { width: 100%; height: 100%; border: 1px dashed #cbd5e1; display: grid; place-items: center; font-size: 9px; color: #94a3b8; }
</style>

<div class="container-fluid px-2 px-md-4 py-3">
    <div class="row justify-content-center">
        <div class="col-xl-9">

            <div class="d-flex align-items-center justify-content-between mb-4">
                <h3 class="fw-bold mb-0">Settings &amp; Assets</h3>
                <span class="hint fs-5" data-bs-toggle="tooltip" title="Everything here is shared by all certificates. Save once and every layout and printout updates."><i class="bi bi-info-circle"></i></span>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Signatory -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <div class="section-title"><span class="dot"><i class="bi bi-person-badge-fill"></i></span> Punong Barangay</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Full name</label>
                            <input type="text" name="captain_name" class="form-control" value="{{ old('captain_name', $settings->captain_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Title</label>
                            <input type="text" name="captain_title" class="form-control" value="{{ old('captain_title', $settings->captain_title) }}" required>
                        </div>
                    </div>

                    <div class="mt-3 p-3 rounded-3 d-flex align-items-center gap-3" style="background: var(--ec-50);">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="showSignatureSwitch" name="show_signature" value="1" {{ $settings->show_signature ? 'checked' : '' }}>
                        </div>
                        <label class="fw-semibold mb-0" for="showSignatureSwitch"><i class="bi bi-pen-fill text-primary me-1"></i> Digital signature on prints</label>
                        <span class="hint ms-auto" data-bs-toggle="tooltip" title="When off, the signature image is hidden so a wet signature can be added."><i class="bi bi-question-circle"></i></span>
                    </div>
                </div>

                <!-- Global Header & Footer -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <div class="section-title">
                        <span class="dot"><i class="bi bi-layout-text-window-reverse"></i></span> Header &amp; Footer
                        <span class="hint ms-auto fs-6" data-bs-toggle="tooltip" title="Applies to every certificate automatically when saved."><i class="bi bi-globe2"></i> Global</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label small"><i class="bi bi-card-heading me-1 text-primary"></i> Header lines
                                    <span class="hint ms-1" data-bs-toggle="tooltip" title="One line per row, up to 6."><i class="bi bi-info-circle"></i></span>
                                </label>
                                <textarea name="header_lines" id="headerLines" rows="4" class="form-control" required>{{ old('header_lines', $settings->header_lines) }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small"><i class="bi bi-fonts me-1 text-primary"></i> Office line</label>
                                <input type="text" name="header_office" id="headerOffice" class="form-control" value="{{ old('header_office', $settings->header_office) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small"><i class="bi bi-chat-square-text me-1 text-primary"></i> Footer label</label>
                                <input type="text" name="footer_label" id="footerLabel" class="form-control" value="{{ old('footer_label', $settings->footer_label) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small"><i class="bi bi-geo-alt me-1 text-primary"></i> Footer address</label>
                                <input type="text" name="footer_address" id="footerAddress" class="form-control" value="{{ old('footer_address', $settings->footer_address) }}" required>
                            </div>
                            <div>
                                <label class="form-label small"><i class="bi bi-envelope me-1 text-primary"></i> Footer email</label>
                                <input type="email" name="footer_email" id="footerEmail" class="form-control" value="{{ old('footer_email', $settings->footer_email) }}">
                            </div>
                        </div>

                        <!-- Live preview -->
                        <div class="col-lg-6">
                            <div class="small fw-semibold text-muted mb-2"><i class="bi bi-eye me-1"></i> Preview</div>
                            <div class="paper-preview">
                                <div class="pv-header">
                                    <div class="pv-logo">@if($settings->lgu_logo)<img src="{{ asset('storage/' . $settings->lgu_logo) }}" alt="">@else<div class="ph">LGU</div>@endif</div>
                                    <div class="pv-lines" id="pvLines"></div>
                                    <div class="pv-logo">@if($settings->brgy_logo)<img src="{{ asset('storage/' . $settings->brgy_logo) }}" alt="">@else<div class="ph">Brgy</div>@endif</div>
                                </div>
                                <hr class="pv-rule">
                                <div class="pv-office" id="pvOffice"></div>

                                <div class="text-center text-muted my-4 small" style="letter-spacing: .2em;">· · ·</div>

                                <div class="pv-footer">
                                    <div class="pv-qr">@if($settings->qr_code)<img src="{{ asset('storage/' . $settings->qr_code) }}" alt="">@else<div class="ph">QR</div>@endif</div>
                                    <div>
                                        <span class="d-block" id="pvLabel" style="letter-spacing: .5px;"></span>
                                        <span class="d-block fw-bold text-muted" id="pvAddress"></span>
                                        <span class="text-primary" id="pvEmail"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image assets -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <div class="section-title"><span class="dot"><i class="bi bi-images"></i></span> Images</div>
                    <div class="row g-3">
                        @foreach([
                            ['lgu_logo', 'LGU logo', 'bi-building'],
                            ['brgy_logo', 'Barangay logo', 'bi-shield-fill-check'],
                            ['qr_code', 'Footer QR', 'bi-qr-code'],
                            ['captain_signature', 'Signature (PNG)', 'bi-pen'],
                        ] as [$field, $label, $icon])
                            <div class="col-md-6">
                                <div class="asset-box">
                                    <label class="form-label small mb-2"><i class="bi {{ $icon }} text-primary me-1"></i> {{ $label }}</label>
                                    @if($settings->$field)
                                        <div class="mb-2"><img src="{{ asset('storage/' . $settings->$field) }}" alt="{{ $label }}"></div>
                                    @endif
                                    <input type="file" name="{{ $field }}" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4 py-2"><i class="bi bi-check2-circle me-1"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const $ = (id) => document.getElementById(id);

        function render() {
            const lines = $('headerLines').value.split(/\r?\n/).map(l => l.trim()).filter(Boolean).slice(0, 6);
            $('pvLines').replaceChildren(...lines.flatMap((l, i) => {
                const span = document.createElement('span'); span.textContent = l;
                return i < lines.length - 1 ? [span, document.createElement('br')] : [span];
            }));
            $('pvOffice').textContent = $('headerOffice').value;
            $('pvLabel').textContent = $('footerLabel').value;
            $('pvAddress').textContent = $('footerAddress').value;
            $('pvEmail').textContent = $('footerEmail').value;
        }

        ['headerLines', 'headerOffice', 'footerLabel', 'footerAddress', 'footerEmail'].forEach(id => $(id).addEventListener('input', render));
        render();

    });
</script>
@endsection
