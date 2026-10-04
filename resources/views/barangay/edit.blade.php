@extends('layouts.app')

@section('content')
<style>
    .pv-card { background: #fff; border: 1px solid var(--ec-line); border-radius: 1rem; padding: 1.1rem 1.3rem; box-shadow: 0 10px 30px -22px rgba(30, 64, 175, .6); }
    .pv-lines { font-family: 'Copperplate Gothic Bold', 'Times New Roman', serif; font-weight: bold; font-size: 9.5pt; line-height: 1.3; text-align: center; color: #000; }
    .pv-chip { display: flex; align-items: center; gap: .6rem; padding: .55rem .75rem; border-radius: .8rem; background: var(--ec-50); font-size: .86rem; color: var(--ec-text); }
    .pv-chip i { color: var(--ec-600); }
</style>

<div class="container-fluid px-2 px-md-4 py-3">
    <div class="row justify-content-center">
        <div class="col-xl-9">

            <div class="d-flex align-items-center justify-content-between mb-4">
                <h3 class="fw-bold mb-0">Barangay</h3>
                <span class="hint fs-5" data-bs-toggle="tooltip"
                      title="Updates the login page, sidebar, footer, certificate header and footer, certificate texts and printed reports. Use this to set up the system for another barangay."><i class="bi bi-info-circle"></i></span>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}</div>
            @endif

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm p-4 h-100">
                        <form action="{{ route('barangay.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label small"><i class="bi bi-geo-alt-fill text-primary me-1"></i> Barangay</label>
                                <input type="text" name="barangay_name" id="inBrgy" class="form-control" maxlength="100"
                                       value="{{ old('barangay_name', $identity['barangay']) }}" placeholder="e.g. San Isidro" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small"><i class="bi bi-buildings-fill text-primary me-1"></i> Municipality</label>
                                <input type="text" name="municipality" id="inMun" class="form-control" maxlength="100"
                                       value="{{ old('municipality', $identity['municipality']) }}" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small"><i class="bi bi-map-fill text-primary me-1"></i> Province</label>
                                <input type="text" name="province" id="inProv" class="form-control" maxlength="100"
                                       value="{{ old('province', $identity['province']) }}" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Apply these names to the whole system?');">
                                <i class="bi bi-check2-circle me-1"></i> Save
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="small fw-semibold text-muted mb-2"><i class="bi bi-eye me-1"></i> Preview</div>
                    <div class="pv-card mb-3">
                        <div class="pv-lines">
                            <div>REPUBLIC OF THE PHILIPPINES</div>
                            <div id="pvProv"></div>
                            <div id="pvMun"></div>
                            <div id="pvBrgy"></div>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <div class="pv-chip"><i class="bi bi-box-arrow-in-right"></i><span id="pvLogin"></span></div>
                        <div class="pv-chip"><i class="bi bi-file-earmark-text"></i><span id="pvText"></span></div>
                        <div class="pv-chip"><i class="bi bi-geo-alt"></i><span id="pvFooter"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const $ = (id) => document.getElementById(id);
        // Same clean-up the server applies
        const tidy = (v, re) => v.replace(/\s+/g, ' ').trim().replace(re, '').trim();

        function render() {
            const b = tidy($('inBrgy').value, /^(barangay|brgy\.?)\s+/i) || '…';
            const m = tidy($('inMun').value, /^(municipality of|town of)\s+/i) || '…';
            const p = tidy($('inProv').value, /^province of\s+/i) || '…';
            $('pvProv').textContent = ('PROVINCE OF ' + p).toUpperCase();
            $('pvMun').textContent = ('MUNICIPALITY OF ' + m).toUpperCase();
            $('pvBrgy').textContent = ('BARANGAY OF ' + b).toUpperCase();
            $('pvLogin').textContent = 'Barangay ' + b;
            $('pvText').textContent = '…resident of Barangay ' + b + ', ' + m + ', ' + p + '.';
            $('pvFooter').textContent = 'Brgy. ' + b + ', ' + m + ', ' + p;
        }
        ['inBrgy', 'inMun', 'inProv'].forEach(id => $(id).addEventListener('input', render));
        render();
    });
</script>
@endsection
