@extends('layouts.app')

@section('content')
@php $hasRibbon = in_array($type, ['clearance', 'residency', 'indigency']); @endphp
<style>
    .chip { border: 1px solid var(--ec-200); background: #fff; color: var(--ec-700); border-radius: 999px; padding: .2rem .7rem; font-size: .78rem; font-weight: 600; cursor: pointer; transition: all .15s; }
    .chip:hover { background: var(--ec-100); }
    #bodyInput { min-height: 340px; line-height: 1.55; }
    .paper { background: #fff; border: 1px solid var(--ec-line); border-radius: .6rem; padding: 26px 30px 22px; box-shadow: 0 18px 40px -26px rgba(30, 64, 175, .55); position: relative; }
    .pv-header { display: flex; align-items: center; justify-content: center; gap: 18px; }
    .pv-logo { width: 54px; height: 54px; flex: 0 0 54px; }
    .pv-logo img { width: 100%; height: 100%; object-fit: contain; }
    .pv-logo .ph { width: 100%; height: 100%; border: 1px dashed #cbd5e1; border-radius: 50%; display: grid; place-items: center; font-size: 8px; color: #94a3b8; }
    .pv-lines { font-family: 'Copperplate Gothic Bold', 'Times New Roman', serif; font-weight: bold; font-size: 8.5pt; line-height: 1.25; text-align: center; color: #000; }
    .pv-office { font-family: 'Edwardian Script ITC', 'Brush Script MT', cursive; font-size: 17pt; font-weight: bold; text-align: center; color: #000; }
    .pv-rule { border: 0; border-top: 2px solid #000; opacity: 1; margin: 5px 0 0; }
    .pv-title { position: relative; text-align: center; margin: 8px auto 14px; }
    .pv-title img { width: 92%; }
    .pv-title h4 { font-family: 'Algerian', 'Times New Roman', serif; font-size: 13pt; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; margin: 0; color: #000; }
    .pv-title.ribbon h4 { position: absolute; top: 52%; left: 50%; transform: translate(-50%, -50%); white-space: nowrap; }
    .pv-body { font-family: 'Century Gothic', Arial, sans-serif; font-size: 9.5pt; line-height: 1.55; text-align: justify; color: #000; }
    .pv-body p { text-indent: 28px; margin-bottom: 8px; }
    .pv-body .sal { text-indent: 0; font-weight: bold; }
    .pv-body ul { list-style: none; padding-left: 26px; margin-bottom: 8px; }
    .pv-sign { text-align: right; font-family: 'Century Gothic', Arial, sans-serif; font-size: 9.5pt; margin-top: 16px; }
    .pv-footer { display: flex; align-items: center; gap: 10px; font-family: 'Century Gothic', Arial, sans-serif; font-size: 7pt; margin-top: 22px; }
    .pv-qr { width: 44px; height: 44px; flex: 0 0 44px; }
    .pv-qr img { width: 100%; height: 100%; object-fit: contain; }
    .pv-qr .ph { width: 100%; height: 100%; border: 1px dashed #cbd5e1; display: grid; place-items: center; font-size: 8px; color: #94a3b8; }
</style>

<div class="container-fluid px-2 px-md-4 py-3">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('templates.edit', $type) }}" class="btn btn-outline-secondary btn-sm" title="Back to layout"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h3 class="fw-bold mb-0">{{ $label }}</h3>
                <div class="small text-muted"><i class="bi bi-pencil-square me-1"></i>Body
                    @if($isCustom)<span class="badge bg-primary bg-opacity-10 ms-1">Customised</span>@endif
                </div>
            </div>
        </div>

        @if($isCustom)
            <form action="{{ route('templates.reset', $type) }}" method="POST" onsubmit="return confirm('Restore the default text for this certificate?');" class="m-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-secondary btn-sm" title="Restore default text"><i class="bi bi-arrow-counterclockwise me-1"></i> Default</button>
            </form>
        @endif
    </div>

    @if ($errors->any())
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}</div>
    @endif

    <div class="row g-4">
        <!-- Editor -->
        <div class="col-xl-5">
            <div class="card border-0 shadow-sm p-4">
                <form action="{{ route('templates.update', $type) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small"><i class="bi bi-type-h1 text-primary me-1"></i> Title</label>
                            <input type="text" name="title" id="titleInput" class="form-control" value="{{ old('title', $template->title) }}" required>
                        </div>
                        @if($type !== 'oath')
                        <div class="col-md-5">
                            <label class="form-label small"><i class="bi bi-chat-left-quote text-primary me-1"></i> Salutation</label>
                            <input type="text" name="salutation" id="salInput" class="form-control" value="{{ old('salutation', $template->salutation) }}">
                        </div>
                        @else
                            <input type="hidden" name="salutation" id="salInput" value="">
                        @endif
                    </div>

                    <div class="mb-2 d-flex align-items-center justify-content-between">
                        <label class="form-label small mb-0"><i class="bi bi-file-text text-primary me-1"></i> Body</label>
                        <span class="hint" data-bs-toggle="tooltip" data-bs-html="true"
                              title="Blank line = new paragraph<br>**bold**<br>Lines starting with 1. 2. 3. become a numbered list"><i class="bi bi-info-circle"></i></span>
                    </div>
                    <textarea name="body_content" id="bodyInput" class="form-control" required>{{ old('body_content', $template->body_content) }}</textarea>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @foreach($shortcodes as $code => $desc)
                            <button type="button" class="chip" data-code="{{ '{' . $code . '}' }}" data-bs-toggle="tooltip" title="{{ $desc }}">{{ '{' . $code . '}' }}</button>
                        @endforeach
                    </div>

                    @if($type === 'jobseeker')
                        @php $w = $settings->witness(); @endphp
                        <div class="mt-4 p-3 rounded-4" style="background: var(--ec-50);">
                            <div class="d-flex align-items-center gap-2 fw-semibold mb-3">
                                <span class="stat-dot"><i class="bi bi-person-check-fill"></i></span> Default witness
                                <span class="hint ms-auto" data-bs-toggle="tooltip" title="Pre-filled as &quot;Witnessed by&quot; when issuing a Job Seeker certificate. It can still be changed per certificate."><i class="bi bi-info-circle"></i></span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label class="form-label small"><i class="bi bi-person text-primary me-1"></i> Name</label>
                                    <input type="text" name="witness_name" id="witnessName" class="form-control" value="{{ old('witness_name', $w['name']) }}" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small"><i class="bi bi-award text-primary me-1"></i> Title</label>
                                    <input type="text" name="witness_title" id="witnessTitle" class="form-control" value="{{ old('witness_title', $w['title']) }}" required>
                                </div>
                            </div>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary w-100 mt-4"><i class="bi bi-check2-circle me-1"></i> Save</button>
                </form>
            </div>
        </div>

        <!-- Live preview -->
        <div class="col-xl-7">
            <div class="small fw-semibold text-muted mb-2"><i class="bi bi-eye me-1"></i> Live preview</div>
            <div class="paper">
                <div class="pv-header">
                    <div class="pv-logo">@if($settings->lgu_logo)<img src="{{ asset('storage/' . $settings->lgu_logo) }}" alt="">@else<div class="ph">LGU</div>@endif</div>
                    <div class="pv-lines">
                        @foreach($settings->header_lines_list as $line)<span>{{ $line }}</span>@if(!$loop->last)<br>@endif @endforeach
                    </div>
                    <div class="pv-logo">@if($settings->brgy_logo)<img src="{{ asset('storage/' . $settings->brgy_logo) }}" alt="">@else<div class="ph">Brgy</div>@endif</div>
                </div>
                <hr class="pv-rule">
                <div class="pv-office">{{ $settings->header_office }}</div>

                <div class="pv-title {{ $hasRibbon ? 'ribbon' : '' }}">
                    @if($hasRibbon)<img src="{{ asset('images/ribbon.jpg') }}" alt="">@endif
                    <h4 id="pvTitle"></h4>
                </div>

                <div class="pv-body">
                    <p class="sal" id="pvSal"></p>
                    <div id="pvBody"></div>
                </div>

                <div class="pv-sign">
                    <strong class="d-block text-uppercase">{{ $settings->captain_name }}</strong>
                    {{ $settings->captain_title }}
                </div>

                @if($type === 'jobseeker')
                    <div class="pv-sign" style="text-align: center; margin-top: 12px;">
                        <div style="text-align: left;">Witnessed by:</div>
                        <strong class="d-block text-uppercase mt-2" id="pvWitnessName"></strong>
                        <span id="pvWitnessTitle"></span>
                    </div>
                @endif

                <div class="pv-footer">
                    <div class="pv-qr">@if($settings->qr_code)<img src="{{ asset('storage/' . $settings->qr_code) }}" alt="">@else<div class="ph">QR</div>@endif</div>
                    <div>
                        <span class="d-block">{{ $settings->footer_label }}</span>
                        <span class="d-block fw-bold text-muted">{{ $settings->footer_address }}</span>
                        @if($settings->footer_email)<span class="text-primary">{{ $settings->footer_email }}</span>@endif
                    </div>
                </div>
            </div>
            <div class="small text-muted mt-2"><i class="bi bi-layout-text-window-reverse me-1"></i> Header &amp; footer come from <a href="{{ route('settings.edit') }}">Settings &amp; Assets</a>.</div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const $ = (id) => document.getElementById(id);
        const body = $('bodyInput');

        // Same placeholder values the PHP renderer uses for real residents
        const sample = {
            name: 'MR. JUAN D. DELA CRUZ', standard_name: 'JUAN D. DELA CRUZ', age: '27', gender: 'male',
            civil_status: 'single', purok: '3', purpose: 'employment', day: '2nd', month_year: 'October 2026',
            ...@json($settings->identity())
        };
        const esc = (s) => s.replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const inline = (t) => esc(t)
            .replace(/\{(\w+)\}/g, (m, k) => (k in sample) ? esc(sample[k]) : m)
            .replace(/\*\*(.+?)\*\*/gs, '<strong>$1</strong>');

        function renderBody(text) {
            return text.replace(/\r\n?/g, '\n').trim().split(/\n{2,}/).map(block => {
                const lines = block.trim().split('\n');
                if (lines.length && lines.every(l => /^\s*\d+\.\s/.test(l))) {
                    return '<ul>' + lines.map(l => { const m = l.match(/^\s*(\d+\.)\s*(.*)$/); return '<li><strong>' + esc(m[1]) + '</strong> ' + inline(m[2]) + '</li>'; }).join('') + '</ul>';
                }
                return '<p>' + inline(lines.map(l => l.trim()).join(' ')) + '</p>';
            }).join('');
        }

        function render() {
            $('pvTitle').textContent = $('titleInput').value;
            const sal = $('salInput').value.trim();
            $('pvSal').textContent = sal; $('pvSal').style.display = sal ? '' : 'none';
            $('pvBody').innerHTML = renderBody(body.value);
            if ($('witnessName')) {
                $('pvWitnessName').textContent = $('witnessName').value;
                $('pvWitnessTitle').textContent = $('witnessTitle').value;
            }
        }

        ['titleInput', 'salInput', 'bodyInput', 'witnessName', 'witnessTitle'].forEach(id => $(id) && $(id).addEventListener('input', render));
        render();

        // Click a chip to insert its {shortcode} at the cursor
        document.querySelectorAll('.chip').forEach(chip => chip.addEventListener('click', function () {
            const code = this.dataset.code, s = body.selectionStart, e = body.selectionEnd;
            body.value = body.value.slice(0, s) + code + body.value.slice(e);
            body.focus(); body.selectionStart = body.selectionEnd = s + code.length;
            render();
        }));

    });
</script>
@endsection
