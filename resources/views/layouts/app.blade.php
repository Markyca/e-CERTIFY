<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'e-CERTIFY') }}</title>

    <!-- Bootstrap 5 CSS, Icons & Inter font (all served locally, no internet needed) -->
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/inter/inter.css') }}">
    <!-- e-CERTIFY theme (white + blue) -->
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ config('app.version') }}">

    <!-- Apply the saved sidebar state before first paint (no flicker) -->
    <script>
        try { if (localStorage.getItem('sidebarState') === 'collapsed') document.documentElement.classList.add('sidebar-collapsed'); } catch (e) {}
    </script>
</head>
<body>
    <div id="app" class="d-flex min-vh-100">

        @auth
        @php
            $navUser    = auth()->user();
            $isAdmin    = $navUser->role === 'Admin';
            $isStaffUp  = in_array($navUser->role, ['Admin', 'Secretary']);
            $onCertMenu = request()->routeIs('templates.*') || request()->routeIs('settings.edit');
            $tplActive  = fn ($t) => request()->routeIs('templates.*') && request()->route('type') === $t;
            $onResidents = request()->routeIs('residents.*') || request()->routeIs('certificates.create') || request()->routeIs('certificates.show');
        @endphp

        <!-- Mobile menu button (the top bar no longer exists) -->
        <button type="button" class="mobile-menu-btn d-print-none" id="mobileMenuBtn" aria-label="Open menu"><i class="bi bi-list"></i></button>
        <div class="sb-backdrop d-print-none" id="sbBackdrop"></div>

        <aside class="sidebar d-print-none" id="appSidebar">

            <!-- Brand + collapse / expand button -->
            <div class="sb-brand">
                <a href="{{ route('dashboard') }}" class="sb-logo" title="e-CERTIFY">
                    <span class="sb-logo-mark"><i class="bi bi-patch-check-fill"></i></span>
                    <span class="sb-logo-text">
                        <strong>e-CERTIFY</strong>
                        <small>{{ $brgy->identity()['barangay'] }}</small>
                    </span>
                </a>
                <button type="button" class="sb-toggle" id="sidebarToggle" title="Collapse / expand sidebar" aria-label="Collapse or expand sidebar">
                    <i class="bi bi-chevron-double-left when-expanded"></i>
                    <i class="bi bi-chevron-double-right when-collapsed"></i>
                </button>
            </div>

            <nav class="sb-nav">
                <div class="sb-label">Menu</div>

                <a href="{{ route('dashboard') }}" data-nav-key="dashboard" @if(request()->routeIs('dashboard')) data-current="1" @endif
                   class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                    <i class="bi bi-grid-1x2-fill sb-icon"></i><span>Dashboard</span>
                </a>

                <a href="{{ route('residents.index') }}" data-nav-key="residents" @if($onResidents) data-current="1" @endif
                   class="sb-link {{ $onResidents ? 'active' : '' }}" title="Residents">
                    <i class="bi bi-people-fill sb-icon"></i><span>Residents</span>
                </a>

                <a href="{{ route('certificates.history') }}" data-nav-key="history" @if(request()->routeIs('certificates.history*')) data-current="1" @endif
                   class="sb-link {{ request()->routeIs('certificates.history*') ? 'active' : '' }}" title="History">
                    <i class="bi bi-clock-history sb-icon"></i><span>History</span>
                </a>

                @if($isStaffUp)
                    <a href="#certificatesMenu" data-bs-toggle="collapse" data-nav-group="certificates"
                       class="sb-link sb-group {{ $onCertMenu ? '' : 'collapsed' }}" aria-expanded="{{ $onCertMenu ? 'true' : 'false' }}" title="Certificates">
                        <i class="bi bi-file-earmark-richtext-fill sb-icon"></i><span>Certificates</span>
                        <i class="bi bi-chevron-down chevron"></i>
                    </a>
                    <div class="collapse {{ $onCertMenu ? 'show' : '' }}" id="certificatesMenu">
                        <div class="sb-sub">
                            @foreach(['clearance' => 'Barangay Clearance', 'residency' => 'Residency', 'indigency' => 'Indigency', 'jobseeker' => 'First Time Job Seeker', 'oath' => 'Oath of Undertaking'] as $key => $label)
                                <a href="{{ route('templates.edit', $key) }}" data-nav-key="tpl-{{ $key }}" data-nav-parent="certificates" @if($tplActive($key)) data-current="1" @endif
                                   class="sb-link {{ $tplActive($key) ? 'active' : '' }}" title="{{ $label }}">
                                    <i class="bi bi-file-earmark-text sb-icon"></i><span>{{ $label }}</span>
                                </a>
                            @endforeach
                            <a href="{{ route('settings.edit') }}" data-nav-key="settings" data-nav-parent="certificates" @if(request()->routeIs('settings.edit')) data-current="1" @endif
                               class="sb-link {{ request()->routeIs('settings.edit') ? 'active' : '' }}" title="Settings & Assets">
                                <i class="bi bi-sliders sb-icon"></i><span>Settings & Assets</span>
                            </a>
                        </div>
                    </div>
                @endif

                @if($isAdmin)
                    <div class="sb-label">Admin</div>

                    <a href="{{ route('logs.index') }}" data-nav-key="logs" @if(request()->routeIs('logs.*')) data-current="1" @endif
                       class="sb-link {{ request()->routeIs('logs.*') ? 'active' : '' }}" title="System Logs">
                        <i class="bi bi-journal-text sb-icon"></i><span>System Logs</span>
                    </a>

                    <a href="{{ route('barangay.edit') }}" data-nav-key="barangay" @if(request()->routeIs('barangay.*')) data-current="1" @endif
                       class="sb-link {{ request()->routeIs('barangay.*') ? 'active' : '' }}" title="Barangay">
                        <i class="bi bi-geo-alt-fill sb-icon"></i><span>Barangay</span>
                    </a>

                    <a href="{{ route('users.index') }}" data-nav-key="users" @if(request()->routeIs('users.*')) data-current="1" @endif
                       class="sb-link {{ request()->routeIs('users.*') ? 'active' : '' }}" title="Users">
                        <i class="bi bi-person-gear sb-icon"></i><span>Users</span>
                    </a>
                @endif
            </nav>

            <!-- Bottom-left: profile, logout and version, grouped -->
            <div class="sb-user">
                <div class="sb-user-row">
                    <span class="sb-avatar" title="{{ $navUser->name }}">{{ strtoupper(mb_substr($navUser->name, 0, 1)) }}</span>
                    <span class="sb-user-meta">
                        <strong>{{ $navUser->name }}</strong>
                        <small>{{ $navUser->role }}</small>
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="sb-logout" title="Log out" aria-label="Log out"><i class="bi bi-box-arrow-right"></i></button>
                    </form>
                </div>
                <div class="sb-version" title="Software version">v{{ config('app.version') }}</div>
            </div>
        </aside>
        @endauth

        <!-- Main Content Area -->
        <div class="main-wrapper">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 d-print-none" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3 d-print-none" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Page Content Injection -->
            <main class="p-4 p-md-5 flex-grow-1">
                @yield('content')
            </main>

            <footer class="app-footer d-print-none mt-auto">
                &copy; {{ date('Y') }} Brgy. {{ $brgy->identity()['barangay'] }} &middot; by <b>Mark Indayon</b>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const root = document.documentElement;
            const body = document.body;

            // ---- Collapse / expand sidebar (remembered) ----
            const toggle = document.getElementById('sidebarToggle');
            if (toggle) {
                toggle.addEventListener('click', function () {
                    root.classList.toggle('sidebar-collapsed');
                    try { localStorage.setItem('sidebarState', root.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded'); } catch (e) {}
                });
            }
            // Enable animations only after first paint
            requestAnimationFrame(function () { body.classList.add('sb-ready'); });

            // ---- Show / hide password (every password box gets an eye button) ----
            document.querySelectorAll('input[type="password"]').forEach(function (input) {
                const host = input.parentElement;
                if (getComputedStyle(host).position === 'static') host.style.position = 'relative';

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'pw-eye';
                btn.setAttribute('aria-label', 'Show password');
                btn.setAttribute('title', 'Show password');
                btn.innerHTML = '<i class="bi bi-eye"></i>';
                host.appendChild(btn);
                input.classList.add('has-eye');

                const place = function () { btn.style.top = (input.offsetTop + input.offsetHeight / 2) + 'px'; };
                place();
                window.addEventListener('resize', place);
                window.addEventListener('load', place);

                btn.addEventListener('click', function () {
                    const reveal = input.type === 'password';
                    input.type = reveal ? 'text' : 'password';
                    btn.firstElementChild.className = reveal ? 'bi bi-eye-slash' : 'bi bi-eye';
                    const label = reveal ? 'Hide password' : 'Show password';
                    btn.setAttribute('aria-label', label);
                    btn.setAttribute('title', label);
                    place();
                    input.focus();
                });
            });

            // ---- Tooltips (used for the small info icons) ----
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) { new bootstrap.Tooltip(el); });

            // ---- Mobile drawer ----
            const openBtn = document.getElementById('mobileMenuBtn');
            const backdrop = document.getElementById('sbBackdrop');
            if (openBtn) openBtn.addEventListener('click', function () { body.classList.add('sidebar-open'); });
            if (backdrop) backdrop.addEventListener('click', function () { body.classList.remove('sidebar-open'); });

            // ---- Remember the last visited menu item (per user) ----
            @auth
            const storeKey = 'ec.lastNav.{{ auth()->id() }}';
            const current = document.querySelector('.sb-nav [data-nav-key][data-current="1"]');

            if (current && current.dataset.navKey !== 'dashboard') {
                // Any page other than the dashboard: remember where the user is
                try { localStorage.setItem(storeKey, current.dataset.navKey); } catch (e) {}
            } else if (current && current.dataset.navKey === 'dashboard') {
                // Back on the dashboard: highlight the item they used last
                let last = null;
                try { last = localStorage.getItem(storeKey); } catch (e) {}
                const el = last ? document.querySelector('.sb-nav [data-nav-key="' + last + '"]') : null;
                if (el) {
                    el.classList.add('is-last');
                    el.setAttribute('title', (el.getAttribute('title') || '') + ' · last visited');
                    const parent = el.dataset.navParent;
                    if (parent) {
                        const panel = document.getElementById('certificatesMenu');
                        if (panel && window.bootstrap) bootstrap.Collapse.getOrCreateInstance(panel, { toggle: false }).show();
                    }
                }
            }
            @endauth
        });
    </script>
</body>
</html>
