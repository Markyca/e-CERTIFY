<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'e-CERTIFY') }}</title>

    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        /* Sidebar Styling & Transition */
        .sidebar {
            width: 260px;
            background: #1e293b; /* Deep slate blue */
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            transition: width 0.3s ease;
            overflow-x: hidden;
        }
        .sidebar .nav-link {
            color: #cbd5e1;
            border-radius: 0.375rem;
            padding: 0.6rem 1rem;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: #ffffff;
        }
        .sidebar-collapse-menu .nav-link {
            padding-left: 2.5rem; /* Slightly tighter indent if desired */
            font-size: 0.9rem;
            color: #94a3b8;
            box-sizing: border-box; /* Locks the padding inside the box model */
            transition: none; /* Disables sudden layout snapping */
        }
        
        .sidebar-collapse-menu .nav-link:hover {
            color: #ffffff;
            background: transparent;
        }

        /* Dropdown Arrow & Layout Stabilization */
        .sidebar-dropdown-link {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            width: 100%;
        }

        .sidebar-dropdown-link .menu-title {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-grow: 1;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-dropdown-link .chevron-icon {
            flex-shrink: 0;
            margin-left: 0px;
            transition: transform 0.2s ease;
        }

        .custom-line-wrapper {
            position: relative;
        }

        .custom-line-wrapper::before {
            content: '';
            position: absolute;
            left: 5px; /* Change this number to push the vertical line left or right independently */
            top: 0;
            bottom: 0;
            width: 1px;
            background-color: #64748b; /* Secondary border color */
        }

        /* Slim / Collapsed Sidebar State */
        body.sidebar-collapsed .sidebar {
            width: 75px !important;
        }
        body.sidebar-collapsed .sidebar .sidebar-text,
        body.sidebar-collapsed .sidebar .sidebar-category,
        body.sidebar-collapsed .sidebar hr,
        body.sidebar-collapsed .sidebar #certificatesMenu,
        body.sidebar-collapsed .sidebar .chevron-icon {
            display: none !important;
        }
        
        /* Perfect Icon Centering in Slim Mode */
        body.sidebar-collapsed .sidebar .nav-link {
            text-align: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        body.sidebar-collapsed .sidebar .nav-link i {
            margin-right: 0 !important;
            font-size: 1.3rem;
        }
        body.sidebar-collapsed .sidebar .sidebar-dropdown-link .menu-title {
            justify-content: center !important;
        }
        body.sidebar-collapsed .sidebar .d-flex.align-items-center i {
            margin-right: 0 !important;
            font-size: 1.4rem !important;
            margin-left: 2px; /* increase this to push further right, decrease/negative to pull left */
        }
        
        /* Main Layout */
        .main-wrapper {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .top-navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Custom highlight box area for sub-menu items */
        .sub-menu-highlight {
            padding-top: 4px !important;
            padding-bottom: 4px !important;
            padding-left: 10px !important; /* Together with the margin below, totals 2.5rem — matching the un-highlighted indent so text/icon position doesn't shift */
            padding-right: 12px !important;
            margin-left: 30px;      /* Background box starts here, clearing the vertical guide line, without moving the text */
            display: inline-block; /* Keeps the highlight tightly wrapped around the text instead of stretching 100% full-width */
            width: auto;           /* Prevents the highlight box from spanning the entire sidebar width */
        }
    </style>

</head>
<body>
    <div id="app" class="d-flex min-vh-100">
        
       <!-- Sidebar (Only visible to logged-in users) -->
        @auth
        <aside class="sidebar d-none d-md-flex flex-column p-3 text-white position-sticky top-0 h-100 d-print-none">
            <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none px-2">
                <i class="bi bi-shield-check fs-3 text-primary me-2"></i>
                <div class="sidebar-text">
                    <span class="fs-5 fw-bold d-block lh-1">e-CERTIFY</span>
                    <span class="small text-white-50" style="font-size: 0.75rem;">Siempre Viva Sur</span>
                </div>
            </div>
            <hr class="border-secondary">
            
            <ul class="nav nav-pills flex-column mb-auto gap-1">
                
                <!-- MAIN MENU SECTION -->
                <li class="nav-item px-3 mb-1 mt-2 sidebar-category">
                    <span class="text-uppercase text-white-50 fw-bold" style="font-size: 0.65rem; letter-spacing: 0.08rem;">Main Menu</span>
                </li>

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                        <i class="bi bi-speedometer2 me-2"></i> <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>
                
                <!-- Resident Master File -->
                <li>
                    <a href="{{ route('residents.index') }}" class="nav-link {{ request()->routeIs('residents.*') ? 'active' : '' }}" title="Resident Master File">
                        <i class="bi bi-people-fill me-2"></i> <span class="sidebar-text">Resident Master File</span>
                    </a>
                </li>
                
                <!-- Document History -->
                <li class="nav-item">
                    <a href="{{ route('certificates.history') }}" class="nav-link {{ request()->routeIs('certificates.history') ? 'active' : '' }}" title="Document History">
                        <i class="bi bi-clock-history me-2"></i> <span class="sidebar-text">Document History</span>
                    </a>
                </li>

                @if(auth()->check() && in_array(auth()->user()->role, ['Admin', 'Secretary']))
                <!-- Certificate Templates & Global Settings Manager -->
                <li>
                    <a href="#certificatesMenu" 
                    data-bs-toggle="collapse" 
                    class="nav-link sidebar-dropdown-link {{ (request()->routeIs('templates.edit') || request()->routeIs('settings.edit')) ? '' : 'collapsed' }}" 
                    aria-expanded="{{ (request()->routeIs('templates.edit') || request()->routeIs('settings.edit')) ? 'true' : 'false' }}"
                    title="View Certificates">
                        <span class="menu-title">
                            <i class="bi bi-file-earmark-richtext"></i> 
                            <span class="sidebar-text">View Certificates</span>
                        </span>
                        <i id="chevronIcon" class="bi bi-chevron-down chevron-icon sidebar-text"></i>
                    </a>
                    
                    <div class="collapse sidebar-collapse-menu {{ (request()->routeIs('templates.edit') || request()->routeIs('settings.edit')) ? 'show' : '' }}" id="certificatesMenu">
                        <div class="custom-line-wrapper ms-3 ps-2">
                            <ul class="nav flex-column mt-1 ps-1" style="margin-left: -35px;">
                                <li>
                                    <a href="{{ route('templates.edit', 'clearance') }}" class="nav-link py-1 small {{ request()->route('type') === 'clearance' ? 'text-white fw-bold bg-secondary bg-opacity-25 rounded sub-menu-highlight' : 'text-white-50' }}">
                                        <i class="bi bi-file-text me-1"></i> Barangay Clearance
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('templates.edit', 'residency') }}" class="nav-link py-1 small {{ request()->route('type') === 'residency' ? 'text-white fw-bold bg-secondary bg-opacity-25 rounded sub-menu-highlight' : 'text-white-50' }}">
                                        <i class="bi bi-file-text me-1"></i> Certificate of Residency
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('templates.edit', 'indigency') }}" class="nav-link py-1 small {{ request()->route('type') === 'indigency' ? 'text-white fw-bold bg-secondary bg-opacity-25 rounded sub-menu-highlight' : 'text-white-50' }}">
                                        <i class="bi bi-file-text me-1"></i> Certificate of Indigency
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('templates.edit', 'jobseeker') }}" class="nav-link py-1 small {{ request()->route('type') === 'jobseeker' ? 'text-white fw-bold bg-secondary bg-opacity-25 rounded sub-menu-highlight' : 'text-white-50' }}">
                                        <i class="bi bi-file-text me-1"></i> First Time Job Seeker
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('templates.edit', 'oath') }}" class="nav-link py-1 small {{ request()->route('type') === 'oath' ? 'text-white fw-bold bg-secondary bg-opacity-25 rounded sub-menu-highlight' : 'text-white-50' }}">
                                        <i class="bi bi-file-text me-1"></i> Oath of Undertaking
                                    </a>
                                </li>
                                <!-- Dedicated Global Assets & Captain Settings Link -->
                                <li>
                                    <a href="{{ route('settings.edit') }}" class="nav-link py-1 small {{ request()->routeIs('settings.edit') ? 'text-white fw-bold bg-secondary bg-opacity-25 rounded sub-menu-highlight' : 'text-white-50' }}">
                                        <i class="bi bi-sliders me-1"></i> Settings & Assets
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                @endif
                
                @if(auth()->check() && auth()->user()->role === 'Admin')
                    <!-- ADMINISTRATION SECTION -->
                    <li class="nav-item px-3 mb-1 mt-4 sidebar-category">
                        <span class="text-uppercase text-white-50 fw-bold" style="font-size: 0.65rem; letter-spacing: 0.08rem;">Administration</span>
                    </li>

                    <!-- System Logs -->
                    <li>
                        <a href="{{ route('logs.index') }}" class="nav-link {{ request()->routeIs('logs.*') ? 'active' : '' }}" title="System Logs">
                            <i class="bi bi-journal-text me-2"></i> <span class="sidebar-text">System Logs</span>
                        </a>
                    </li>
                
                    <!-- User Management -->
                    <li>
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" title="User Management">
                            <i class="bi bi-person-gear me-2"></i> <span class="sidebar-text">User Management</span>
                        </a>
                    </li>
                @endif
            </ul>
        </aside>
        @endauth

        <!-- Main Content Area -->
        <div class="main-wrapper bg-light">
            
            <!-- Top Navbar (Only visible when authenticated) -->
            @auth
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top d-print-none top-navbar">
                <div class="container-fluid px-4">
                    
                    <!-- Left Side: Sidebar Slim Toggle Button -->
                    <button class="btn btn-outline-secondary btn-sm border-0 me-2" id="sidebarToggle" type="button" title="Toggle Sidebar Width">
                        <i class="bi bi-list fs-5"></i>
                    </button>

                    <!-- Right Side Dropdown -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle fw-semibold text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="bi bi-person-circle fs-5 align-middle me-1 text-primary"></i> 
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-2" aria-labelledby="navbarDropdown">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}" 
                                    class="dropdown-item text-danger fw-semibold" 
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i> Secure Logout
                                    </a>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
            @endauth
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Page Content Injection -->
            <main class="p-4 p-md-5 flex-grow-1">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white text-center py-3 border-top mt-auto d-print-none">
                <div class="container-fluid small text-muted">
                    &copy; {{ date('Y') }} Barangay Siempre Viva Sur Certificate Issuance System. <br>
                    System Developed by <span class="fw-semibold text-primary">Mark Indayon</span>.
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Chevron Rotation
            const menu = document.getElementById('certificatesMenu');
            const chevron = document.getElementById('chevronIcon');

            if (menu && chevron) {
                menu.addEventListener('show.bs.collapse', function () {
                    chevron.classList.remove('bi-chevron-down');
                    chevron.classList.add('bi-chevron-up');
                });

                menu.addEventListener('hide.bs.collapse', function () {
                    chevron.classList.remove('bi-chevron-up');
                    chevron.classList.add('bi-chevron-down');
                });
            }

            // Sidebar Slim Toggle Functionality with LocalStorage Memory
            const sidebarToggle = document.getElementById('sidebarToggle');
            const body = document.body;

            if (localStorage.getItem('sidebarState') === 'collapsed') {
                body.classList.add('sidebar-collapsed');
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function () {
                    body.classList.toggle('sidebar-collapsed');
                    if (body.classList.contains('sidebar-collapsed')) {
                        localStorage.setItem('sidebarState', 'collapsed');
                    } else {
                        localStorage.setItem('sidebarState', 'expanded');
                    }
                });
            }
        });
    </script>
</body>
</html>