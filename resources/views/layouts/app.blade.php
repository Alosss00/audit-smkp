<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SMKP Minerba — Sistem Informasi Audit Internal')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230284c7'><path d='M5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99-1.616 2.113-3.718 3.37-4.7 3.748a1.15 1.15 0 0 1-.694 0c-.981-.378-3.084-1.635-4.7-3.748C.786 10.518-.596 7.2.001 2.72A1.54 1.54 0 0 1 1.044 1.45C1.703 1.235 2.822.88 3.93.58z'/></svg>">

    <!-- Plus Jakarta Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Chart.js 4.4 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --smkp-sidebar-width: 260px;
            --smkp-bg: #f8fafc;
            --smkp-dark-gradient: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            --smkp-accent: #0284c7;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--smkp-bg);
            color: #1e293b;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar-wrapper {
            width: var(--smkp-sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--smkp-dark-gradient);
            z-index: 1030;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand-title {
            font-weight: 800;
            font-size: 1.1rem;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        /* Pagination Styling & SVG Constraint Fix */
        .pagination svg,
        .page-link svg,
        svg.w-5,
        svg.h-5,
        nav svg {
            width: 1rem !important;
            height: 1rem !important;
            max-width: 16px !important;
            max-height: 16px !important;
            display: inline-block !important;
        }

        .pagination {
            margin-bottom: 0;
            gap: 4px;
        }

        .page-item .page-link {
            border-radius: 8px !important;
            color: #0284c7;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            padding: 6px 12px;
            font-size: 0.875rem;
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #0284c7, #0369a1) !important;
            border-color: #0284c7 !important;
            color: #ffffff !important;
        }

        .sidebar-nav {
            padding: 16px 12px;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #334155 transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background-color: #334155;
            border-radius: 99px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background-color: #475569;
        }

        .nav-section-title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin: 16px 12px 8px 12px;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 10px;
            text-decoration: none;
            margin-bottom: 4px;
            transition: all 0.2s ease;
        }

        .sidebar-nav-link i {
            font-size: 1.15rem;
            margin-right: 12px;
            transition: transform 0.2s ease;
        }

        .sidebar-nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
            transform: translateX(3px);
        }

        .sidebar-nav-link.active {
            color: #ffffff;
            background: linear-gradient(135deg, #0284c7, #0369a1);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
        }

        .sidebar-nav-link.active i {
            color: #ffffff;
        }

        .sidebar-user-footer {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(15, 23, 42, 0.4);
        }

        /* Main Content Wrapper */
        .main-content-wrapper {
            margin-left: var(--smkp-sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        @guest
            .main-content-wrapper {
                margin-left: 0 !important;
            }
        @endguest

        .top-header-bar {
            height: 68px;
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .card-custom {
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
            background-color: #ffffff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.07);
        }

        .stat-icon-box {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .badge-role {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 6px 12px;
            border-radius: 50rem;
        }

        /* Responsive Mobile Sidebar Toggle */
        @media (max-width: 991.98px) {
            .sidebar-wrapper {
                margin-left: calc(-1 * var(--smkp-sidebar-width));
            }

            .sidebar-wrapper.show {
                margin-left: 0;
            }

            .main-content-wrapper {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="@auth has-sidebar @endauth">

    @auth
        <!-- Left Sidebar Navigation -->
        <aside class="sidebar-wrapper" id="sidebarWrapper">
            <!-- Brand Header -->
            <div class="sidebar-brand d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: linear-gradient(135deg, #0284c7, #0369a1) !important;">
                        <i class="bi bi-shield-fill-check text-white fs-5"></i>
                    </div>
                    <div>
                        <div class="sidebar-brand-title">SMKP MINERBA</div>
                        <small class="text-slate-400 d-block" style="font-size: 0.72rem; color: #94a3b8;">Audit Internal System</small>
                    </div>
                </div>
                <button class="btn btn-link text-white p-0 d-lg-none" id="sidebarCloseBtn">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="sidebar-nav">
                <!-- Section Utama -->
                <div class="nav-section-title">Utama</div>
                
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('auditor.dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('auditor.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                @endif

                <!-- Section Audit System -->
                <div class="nav-section-title">Penilaian & Monitoring</div>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.audit-sesi.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.audit-sesi.index') || request()->routeIs('admin.audit-sesi.matrix') || request()->routeIs('admin.audit-sesi.rekap') ? 'active' : '' }}">
                        <i class="bi bi-journal-check"></i>
                        <span>Kelola Sesi Audit</span>
                    </a>
                    <a href="{{ route('admin.audit-sesi.create') }}" class="sidebar-nav-link {{ request()->routeIs('admin.audit-sesi.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i>
                        <span>Buat Sesi Baru</span>
                    </a>
                    <a href="{{ route('admin.rekap-audit.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.rekap-audit.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-check"></i>
                        <span>Monitoring Audit</span>
                    </a>
                    <a href="{{ route('admin.pica.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.pica.*') ? 'active' : '' }}">
                        <i class="bi bi-tools"></i>
                        <span>Otoritas & Oversight PICA</span>
                    </a>
                @else
                    <a href="{{ route('auditor.audit-sesi.index') }}" class="sidebar-nav-link {{ request()->routeIs('auditor.audit-sesi.index') || request()->routeIs('auditor.audit-sesi.rekap') ? 'active' : '' }}">
                        <i class="bi bi-journal-check"></i>
                        <span>Sesi Audit Area Saya</span>
                    </a>
                    <a href="{{ route('auditor.pica.index') }}" class="sidebar-nav-link {{ request()->routeIs('auditor.pica.*') ? 'active' : '' }}">
                        <i class="bi bi-tools"></i>
                        <span>Tindak Lanjut PICA</span>
                    </a>
                @endif

                <!-- Section Master Data (Admin Only) -->
                @if(auth()->user()->isAdmin())
                    <div class="nav-section-title">Pengelolaan Master Data</div>

                    <a href="{{ route('admin.perusahaans.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.perusahaans.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i>
                        <span>Master Perusahaan</span>
                    </a>

                    <a href="{{ route('admin.departemens.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.departemens.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3-fill"></i>
                        <span>Master Departemen</span>
                    </a>

                    <a href="{{ route('admin.elemens.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.elemens.*') ? 'active' : '' }}">
                        <i class="bi bi-folder"></i>
                        <span>Master Elemen</span>
                    </a>

                    <a href="{{ route('admin.sub-elemens.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.sub-elemens.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i>
                        <span>Sub-Elemen</span>
                    </a>

                    <a href="{{ route('admin.kriterias.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.kriterias.*') ? 'active' : '' }}">
                        <i class="bi bi-list-check"></i>
                        <span>Master Kriteria</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Kelola User</span>
                    </a>

                    <a href="{{ route('admin.audit-logs.index') }}" class="sidebar-nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Log Aktivitas & Audit File</span>
                    </a>
                @endif
            </div>

            <!-- User Profile Footer -->
            <div class="sidebar-user-footer">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <div class="bg-slate-700 text-white rounded-circle p-2 d-flex align-items-center justify-content-center border border-secondary" style="width: 36px; height: 36px; background: #334155;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="overflow-hidden">
                            <strong class="text-white d-block text-truncate small" style="max-width: 130px;">{{ auth()->user()->name }}</strong>
                            @if(auth()->user()->isAdmin())
                                <span class="badge bg-danger badge-role" style="font-size: 0.65rem; padding: 2px 8px;">Administrator</span>
                            @else
                                <span class="badge bg-info text-dark badge-role" style="font-size: 0.65rem; padding: 2px 8px;">Auditor</span>
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light border-0 text-danger p-2" title="Keluar dari Sistem">
                            <i class="bi bi-box-arrow-right fs-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    @endauth

    <!-- Main Content Area -->
    <div class="main-content-wrapper">
        @auth
            <!-- Top Header Bar -->
            <header class="top-header-bar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light d-lg-none rounded-3 border" id="sidebarOpenBtn">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <span class="fw-bold text-slate-800 d-none d-md-inline" style="font-size: 0.95rem;">
                        <i class="bi bi-calendar-event me-2 text-primary"></i> {{ date('l, d F Y') }}
                    </span>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-slate-100 text-slate-700 border p-2 px-3 rounded-pill d-flex align-items-center gap-2" style="background-color: #f1f5f9;">
                        <i class="bi bi-shield-check text-primary"></i>
                        <span>Kepdirjen ESDM 185</span>
                    </span>
                </div>
            </header>
        @endauth

        <!-- Main Content -->
        <main class="@auth p-4 p-md-4 @endauth flex-grow-1">
            <div class="@auth container-fluid max-width-xl @else h-100 @endauth">
                <!-- Global Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        @auth
            <footer class="bg-white border-top py-3 mt-auto text-center text-muted" style="font-size: 0.85rem;">
                <div class="container-fluid">
                    &copy; {{ date('Y') }} <strong>Sistem Informasi Audit Internal SMKP Minerba</strong> — Kepdirjen 185
                </div>
            </footer>
        @else
            <footer class="text-center text-light opacity-50 py-3 mt-auto" style="font-size: 0.85rem; background: transparent;">
                <div class="container-fluid">
                    &copy; {{ date('Y') }} <strong>Sistem Informasi Audit Internal SMKP Minerba</strong> — Kepdirjen 185
                </div>
            </footer>
        @endauth
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Mobile Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarWrapper = document.getElementById('sidebarWrapper');
            const sidebarOpenBtn = document.getElementById('sidebarOpenBtn');
            const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');

            if (sidebarOpenBtn && sidebarWrapper) {
                sidebarOpenBtn.addEventListener('click', function() {
                    sidebarWrapper.classList.add('show');
                });
            }

            if (sidebarCloseBtn && sidebarWrapper) {
                sidebarCloseBtn.addEventListener('click', function() {
                    sidebarWrapper.classList.remove('show');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
