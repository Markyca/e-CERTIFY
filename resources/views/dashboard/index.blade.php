@extends('layouts.app')

@section('content')
<style>
    /* ---------- Expanding icon search ---------- */
    .ec-search {
        position: relative; display: flex; align-items: center; height: 46px; width: 46px;
        border-radius: 999px; background: #fff; border: 1px solid var(--ec-line);
        box-shadow: 0 6px 18px -10px rgba(30, 64, 175, .45);
        transition: width .38s cubic-bezier(.4, 0, .2, 1), border-color .2s, box-shadow .2s;
    }
    .ec-search.open { width: min(440px, 100%); border-color: var(--ec-300); box-shadow: 0 0 0 .22rem rgba(59, 130, 246, .14), 0 10px 24px -12px rgba(30, 64, 175, .5); }
    .ec-search-input {
        flex: 0 0 0;            /* takes no space while collapsed */
        width: 0;
        min-width: 0;
        padding: 0;             /* was padding-left: 1.1rem, which pushed the icon out */
        opacity: 0;
        pointer-events: none;
        border: 0; outline: 0; background: transparent;
        font-size: .95rem; color: var(--ec-ink);
        transition: opacity .2s ease .1s;
    }
    .ec-search.open .ec-search-input {
        flex: 1 1 auto;
        width: auto;
        padding: 0 0 0 1.1rem;  /* the padding only comes back when open */
        opacity: 1;
        pointer-events: auto;
    }
    .ec-search-btn {
        flex: 0 0 44px; width: 44px; height: 44px; border: 0; border-radius: 50%; background: transparent;
        color: var(--ec-600); font-size: 1.1rem; display: grid; place-items: center; cursor: pointer;
    }
    .ec-search:not(.open) .ec-search-btn:hover { background: var(--ec-50); }
    .ec-search.open .ec-search-btn { color: var(--ec-muted); }
    .ec-search-results {
        position: absolute; top: calc(100% + 10px); right: 0; width: 100%; min-width: 300px; z-index: 1050;
        background: #fff; border: 1px solid var(--ec-line); border-radius: 1rem; padding: .4rem;
        box-shadow: 0 20px 40px -18px rgba(30, 64, 175, .35); max-height: 360px; overflow-y: auto;
    }
    .ec-result { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .55rem .65rem; border-radius: .7rem; }
    .ec-result:hover { background: var(--ec-50); }

    /* ---------- Stat cards ---------- */
    .stat-card { display: flex; align-items: center; gap: 1rem; padding: 1.1rem 1.2rem; transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
    .stat-card:hover { transform: translateY(-3px); border-color: var(--ec-200) !important; box-shadow: 0 16px 30px -16px rgba(37, 99, 235, .45) !important; }
    .stat-icon { flex: 0 0 52px; width: 52px; height: 52px; border-radius: 1rem; display: grid; place-items: center; font-size: 1.45rem; color: var(--ec-600); background: var(--ec-100); }
    .stat-icon.alt { color: #475f86; background: #e9eef7; }
    .stat-label { font-size: .74rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--ec-muted); }
    .stat-value { font-size: 1.85rem; font-weight: 800; color: var(--ec-ink); line-height: 1.1; letter-spacing: -.02em; }

    /* ---------- Charts ---------- */
    .chart-title { display: flex; align-items: center; gap: .6rem; font-weight: 700; color: var(--ec-ink); margin-bottom: 1rem; }
    .chart-title .dot { width: 34px; height: 34px; border-radius: .7rem; display: grid; place-items: center; background: var(--ec-100); color: var(--ec-600); }
    .chart-empty { height: 240px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #a3b1c6; gap: .4rem; }
    .chart-empty i { font-size: 2.2rem; color: var(--ec-200); }
    .legend-item { display: flex; align-items: center; gap: .6rem; padding: .45rem .6rem; border-radius: .7rem; text-decoration: none; color: var(--ec-text); font-size: .88rem; font-weight: 500; }
    .legend-item:hover { background: var(--ec-50); color: var(--ec-700); }
    .legend-swatch { width: 10px; height: 10px; border-radius: 3px; flex: 0 0 10px; }
    .legend-count { margin-left: auto; font-weight: 700; color: var(--ec-ink); }
    .quick-bar .btn { white-space: nowrap; }
</style>

<div class="container-fluid px-2 px-md-4 py-3">

    <!-- Header + icon-only expanding search -->
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-0">Dashboard</h3>
            <div class="text-muted small">Hi, {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }} <span class="ms-1">👋</span></div>
        </div>

        <div class="ec-search" id="ecSearch">
            <input type="text" id="liveSearchInput" class="ec-search-input" placeholder="Find a resident…" autocomplete="off" tabindex="-1">
            <button type="button" class="ec-search-btn" id="ecSearchBtn" aria-label="Search residents" title="Search residents">
                <i class="bi bi-search" id="ecSearchIcon"></i>
            </button>
            <div id="searchResultsDropdown" class="ec-search-results d-none">
                <div id="resultsContainer"></div>
            </div>
        </div>
    </div>

    <!-- Metric cards -->
    <div class="row g-3 g-lg-4 mb-4 justify-content-center">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('residents.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm stat-card h-100">
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="stat-label">Residents</div>
                        <div class="stat-value">{{ number_format($totalResidents) }}</div>
                    </div>
                </div>
            </a>
        </div>

        @if(in_array(auth()->user()->role, ['Admin', 'Secretary']))
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('residents.archived') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm stat-card h-100">
                    <div class="stat-icon alt"><i class="bi bi-archive-fill"></i></div>
                    <div>
                        <div class="stat-label">Archived</div>
                        <div class="stat-value">{{ number_format($totalArchived ?? 0) }}</div>
                    </div>
                </div>
            </a>
        </div>
        @endif

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('certificates.history', ['date' => now()->toDateString()]) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm stat-card h-100">
                    <div class="stat-icon"><i class="bi bi-file-earmark-check-fill"></i></div>
                    <div>
                        <div class="stat-label">Today</div>
                        <div class="stat-value">{{ number_format($certificatesToday) }}</div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('certificates.history', ['month' => now()->month, 'year' => now()->year]) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm stat-card h-100">
                    <div class="stat-icon"><i class="bi bi-calendar2-check-fill"></i></div>
                    <div>
                        <div class="stat-label">This Month</div>
                        <div class="stat-value">{{ number_format($certificatesThisMonth) }}</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="card border-0 shadow-sm px-3 py-3 mb-4 quick-bar">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('residents.create') }}" class="btn btn-primary btn-sm px-3"><i class="bi bi-person-plus-fill me-1"></i> New Resident</a>
            <a href="{{ route('certificates.history') }}" class="btn btn-outline-secondary btn-sm px-3"><i class="bi bi-clock-history me-1"></i> History</a>

            @if(in_array(auth()->user()->role, ['Admin', 'Secretary']))
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-file-earmark-richtext me-1"></i> Templates
                    </button>
                    <ul class="dropdown-menu shadow-sm">
                        <li><a class="dropdown-item small" href="{{ route('templates.edit', 'clearance') }}"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Clearance</a></li>
                        <li><a class="dropdown-item small" href="{{ route('templates.edit', 'residency') }}"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Residency</a></li>
                        <li><a class="dropdown-item small" href="{{ route('templates.edit', 'indigency') }}"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Indigency</a></li>
                        <li><a class="dropdown-item small" href="{{ route('templates.edit', 'jobseeker') }}"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Job Seeker</a></li>
                        <li><a class="dropdown-item small" href="{{ route('templates.edit', 'oath') }}"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Oath</a></li>
                    </ul>
                </div>
            @endif

            <div class="form-check form-switch m-0 ms-auto d-flex align-items-center gap-2" title="Show the digital signature on printed certificates">
                <input class="form-check-input m-0" type="checkbox" role="switch" id="showSignatureSwitch" {{ optional($settings)->show_signature ? 'checked' : '' }} style="cursor: pointer; width: 2.5em; height: 1.25em;">
                <label class="form-check-label small fw-semibold text-nowrap user-select-none" for="showSignatureSwitch" style="cursor: pointer;">
                    <i class="bi bi-pen-fill text-primary me-1"></i>Signature
                </label>
            </div>
        </div>
    </div>

    <!-- Analytics -->
    <div class="row g-3 g-lg-4 mb-4">
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm p-4 h-100">
                <div class="chart-title">
                    <span class="dot"><i class="bi bi-bar-chart-line-fill"></i></span>
                    <span>This Month</span>
                    <i class="bi bi-hand-index-thumb ms-auto hint" data-bs-toggle="tooltip" title="Click a bar to see those records"></i>
                </div>
                <div id="monthWrap" style="position: relative; height: 250px;">
                    <canvas id="certificateMonthChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card border-0 shadow-sm p-4 h-100">
                <div class="chart-title">
                    <span class="dot"><i class="bi bi-pie-chart-fill"></i></span>
                    <span>Today</span>
                    <i class="bi bi-hand-index-thumb ms-auto hint" data-bs-toggle="tooltip" title="Click a slice or label to see those records"></i>
                </div>
                <div id="dayWrap">
                    <div class="row align-items-center g-2">
                        <div class="col-sm-6">
                            <div style="position: relative; height: 210px;"><canvas id="certificateDayChart"></canvas></div>
                        </div>
                        <div class="col-sm-6"><div id="dayLegend"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent issuances -->
    <div class="card border-0 shadow-sm p-4 mb-4">
        <div class="chart-title">
            <span class="dot"><i class="bi bi-receipt"></i></span>
            <span>Recent</span>
            <a href="{{ route('certificates.history') }}" class="ms-auto btn btn-sm btn-outline-primary px-3" title="View all"><i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0 text-nowrap">
                <thead>
                    <tr>
                        <th><i class="bi bi-hash"></i></th>
                        <th><i class="bi bi-person"></i></th>
                        <th><i class="bi bi-file-earmark"></i></th>
                        <th><i class="bi bi-clock"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCertificates as $cert)
                        <tr>
                            <td><span class="font-monospace small">{{ $cert->control_number }}</span></td>
                            <td>{{ optional($cert->resident)->last_name }}, {{ optional($cert->resident)->first_name }}</td>
                            <td><span class="badge bg-primary bg-opacity-10">{{ $cert->certificate_type }}</span></td>
                            <td class="text-muted small">{{ $cert->created_at->format('M d, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4"><i class="bi bi-inbox fs-4 d-block mb-1"></i></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const historyBaseUrl = @json(route('certificates.history'));
    const currentMonth = @json(now()->month);
    const currentYear = @json(now()->year);
    const todayDate = @json(now()->toDateString());

    const monthLabels = @json($chartLabels);
    const monthValues = @json($chartValues);
    const dayLabels = @json($dayLabels ?? []);
    const dayValues = @json($dayValues ?? []);

    const PALETTE = ['#2563eb', '#60a5fa', '#1e40af', '#93c5fd', '#38bdf8', '#818cf8'];

    Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, sans-serif";
    Chart.defaults.color = '#64748b';

    const tooltipStyle = {
        backgroundColor: '#0f172a', titleColor: '#fff', bodyColor: '#dbeafe', padding: 12, cornerRadius: 10,
        displayColors: false, titleFont: { weight: '600' }, boxPadding: 4
    };
    const pointer = (e, els) => { e.native.target.style.cursor = els.length ? 'pointer' : 'default'; };

    function showEmpty(canvasId, wrapId) {
        const wrap = document.getElementById(wrapId);
        wrap.innerHTML = '<div class="chart-empty"><i class="bi bi-bar-chart"></i><span class="small">No certificates yet</span></div>';
    }

    // 1) This month: sleek rounded gradient bars
    if (monthValues.length) {
        new Chart(document.getElementById('certificateMonthChart'), {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    data: monthValues,
                    borderRadius: 12, borderSkipped: false, maxBarThickness: 54,
                    backgroundColor: (c) => {
                        const { ctx, chartArea } = c.chart;
                        if (!chartArea) return '#3b82f6';
                        const g = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        g.addColorStop(0, '#bfdbfe'); g.addColorStop(1, '#2563eb');
                        return g;
                    },
                    hoverBackgroundColor: '#1d4ed8'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, onHover: pointer,
                layout: { padding: { top: 6 } },
                onClick: (e, els) => {
                    if (els.length) window.location.href = historyBaseUrl + '?month=' + currentMonth + '&year=' + currentYear + '&type=' + encodeURIComponent(monthLabels[els[0].index]);
                },
                scales: {
                    x: { grid: { display: false }, border: { display: false }, ticks: { font: { weight: '600' } } },
                    y: { beginAtZero: true, border: { display: false }, grid: { color: '#eef2f9', borderDash: [4, 4] }, ticks: { precision: 0, padding: 8 } }
                },
                plugins: { legend: { display: false }, tooltip: { ...tooltipStyle, callbacks: { label: (i) => i.parsed.y + ' issued' } } }
            }
        });
    } else { showEmpty('certificateMonthChart', 'monthWrap'); }

    // 2) Today: slim doughnut with the total in the middle + clickable legend
    if (dayValues.length) {
        const total = dayValues.reduce((a, b) => a + b, 0);
        const centerText = {
            id: 'centerText',
            afterDraw(chart) {
                const { ctx, chartArea: { left, right, top, bottom } } = chart;
                const x = (left + right) / 2, y = (top + bottom) / 2;
                ctx.save(); ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
                ctx.fillStyle = '#0f172a'; ctx.font = "800 30px 'Inter', sans-serif"; ctx.fillText(total, x, y - 6);
                ctx.fillStyle = '#94a3b8'; ctx.font = "600 11px 'Inter', sans-serif"; ctx.fillText('TODAY', x, y + 18);
                ctx.restore();
            }
        };
        new Chart(document.getElementById('certificateDayChart'), {
            type: 'doughnut',
            data: { labels: dayLabels, datasets: [{ data: dayValues, backgroundColor: dayLabels.map((_, i) => PALETTE[i % PALETTE.length]), borderWidth: 4, borderColor: '#fff', hoverOffset: 6, borderRadius: 6 }] },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '74%', onHover: pointer,
                onClick: (e, els) => { if (els.length) window.location.href = historyBaseUrl + '?date=' + todayDate + '&type=' + encodeURIComponent(dayLabels[els[0].index]); },
                plugins: { legend: { display: false }, tooltip: { ...tooltipStyle, callbacks: { label: (i) => i.label + ': ' + i.parsed } } }
            },
            plugins: [centerText]
        });

        const legend = document.getElementById('dayLegend');
        dayLabels.forEach((label, i) => {
            const a = document.createElement('a');
            a.className = 'legend-item';
            a.href = historyBaseUrl + '?date=' + todayDate + '&type=' + encodeURIComponent(label);
            a.innerHTML = '<span class="legend-swatch" style="background:' + PALETTE[i % PALETTE.length] + '"></span><span></span><span class="legend-count"></span>';
            a.children[1].textContent = label;
            a.children[2].textContent = dayValues[i];
            legend.appendChild(a);
        });
    } else { showEmpty('certificateDayChart', 'dayWrap'); }

</script>

<script>
    // ---------- Expanding quick search ----------
    document.addEventListener('DOMContentLoaded', function () {
        const box = document.getElementById('ecSearch');
        const btn = document.getElementById('ecSearchBtn');
        const icon = document.getElementById('ecSearchIcon');
        const input = document.getElementById('liveSearchInput');
        const dropdown = document.getElementById('searchResultsDropdown');
        const container = document.getElementById('resultsContainer');
        const searchUrl = @json(route('dashboard.search-residents'));
        let timer;

        const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        function openSearch() {
            box.classList.add('open'); icon.className = 'bi bi-x-lg'; input.tabIndex = 0;
            setTimeout(() => input.focus(), 120);
        }
        function closeSearch() {
            box.classList.remove('open'); icon.className = 'bi bi-search'; input.tabIndex = -1;
            input.value = ''; container.innerHTML = ''; dropdown.classList.add('d-none');
        }

        btn.addEventListener('click', () => box.classList.contains('open') ? closeSearch() : openSearch());
        input.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSearch(); });
        document.addEventListener('click', (e) => { if (!box.contains(e.target) && box.classList.contains('open') && !input.value.trim()) closeSearch(); else if (!box.contains(e.target)) dropdown.classList.add('d-none'); });

        input.addEventListener('input', function () {
            clearTimeout(timer);
            const q = this.value.trim();
            if (q.length < 2) { dropdown.classList.add('d-none'); container.innerHTML = ''; return; }

            timer = setTimeout(() => {
                fetch(searchUrl + '?q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(data => {
                        dropdown.classList.remove('d-none');
                        if (!data.length) {
                            container.innerHTML = '<div class="text-center text-muted py-3"><i class="bi bi-person-x fs-4 d-block"></i></div>';
                            return;
                        }
                        container.innerHTML = data.map(r => `
                            <div class="ec-result">
                                <div class="min-w-0">
                                    <div class="fw-semibold text-dark">${esc(r.last_name)}, ${esc(r.first_name)}</div>
                                    <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>Purok ${esc(r.purok ?? 'N/A')}</div>
                                </div>
                                <a href="/residents/${encodeURIComponent(r.id)}/certificates/create" class="btn btn-sm btn-primary px-3" title="Issue document"><i class="bi bi-file-earmark-plus"></i></a>
                            </div>`).join('');
                    })
                    .catch(() => {});
            }, 250);
        });
    });
</script>

<script>
    // ---------- Digital signature switch ----------
    document.addEventListener('DOMContentLoaded', function () {
        const signatureSwitch = document.getElementById('showSignatureSwitch');
        if (!signatureSwitch) return;
        signatureSwitch.addEventListener('change', function () {
            fetch(@json(route('settings.update.ajax')), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
                body: JSON.stringify({ show_signature: this.checked ? 1 : 0 })
            }).then(r => r.json()).catch(err => console.error('Error updating signature setting:', err));
        });
    });
</script>
@endsection
