<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Audit Internal SMKP Minerba')</title>

    <!-- Inline SVG Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230284c7'><path d='M8 0c-.159 0-.307.074-.4.2L1.6 5.6C1.22 5.98 1 6.48 1 7v4c0 3.31 3.13 6 7 6s7-2.69 7-6V7c0-.52-.22-1.02-.6-1.4L8.4.2A.498.498 0 0 0 8 0zm3.354 5.854-4 4a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7 8.793l3.646-3.647a.5.5 0 0 1 .708.708z'/></svg>">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Chart.js 4.4 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --smkp-primary: #0f172a;
            --smkp-accent: #0284c7;
            --smkp-accent-hover: #0369a1;
            --smkp-bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--smkp-bg);
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-custom {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.15);
        }

        .brand-logo {
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-badge {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            font-size: 0.65rem;
            text-transform: uppercase;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 700;
        }

        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: #ffffff;
        }

        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.08);
        }

        .stat-icon-box {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .badge-role {
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }
    </style>
    @stack('styles')
</head>
<body>

    @auth
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand brand-logo" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('auditor.dashboard') }}">
                <i class="bi bi-shield-check text-info fs-3"></i>
                <div>
                    <div>SMKP MINERBA <span class="brand-badge ms-1">Kepdirjen 185</span></div>
                    <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 500;">Sistem Informasi Audit Internal</div>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto ms-lg-4 mb-2 mb-lg-0 gap-1">
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active fw-bold text-info' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.rekap-audit.*') ? 'active fw-bold text-info' : '' }}" href="{{ route('admin.rekap-audit.index') }}">
                                <i class="bi bi-shield-check me-1"></i> Monitoring Audit
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.elemens.*') ? 'active fw-bold text-info' : '' }}" href="{{ route('admin.elemens.index') }}">
                                <i class="bi bi-folder me-1"></i> Master Elemen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.sub-elemens.*') ? 'active fw-bold text-info' : '' }}" href="{{ route('admin.sub-elemens.index') }}">
                                <i class="bi bi-diagram-3 me-1"></i> Sub-Elemen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.kriterias.*') ? 'active fw-bold text-info' : '' }}" href="{{ route('admin.kriterias.index') }}">
                                <i class="bi bi-list-check me-1"></i> Kriteria
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active fw-bold text-info' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="bi bi-people me-1"></i> Kelola User
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('auditor.dashboard') ? 'active fw-bold text-info' : '' }}" href="{{ route('auditor.dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('auditor.audit-sesi.*') ? 'active fw-bold text-info' : '' }}" href="{{ route('auditor.audit-sesi.index') }}">
                                <i class="bi bi-journal-text me-1"></i> Sesi Audit
                            </a>
                        </li>
                    @endif
                </ul>

                <ul class="navbar-nav ms-auto align-items-center gap-3 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <div class="d-flex align-items-center gap-2 text-white">
                            <div class="bg-secondary bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="bi bi-person-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-white" style="font-size: 0.9rem;">{{ auth()->user()->name }}</div>
                                <div class="d-flex align-items-center gap-1">
                                    @if(auth()->user()->isAdmin())
                                        <span class="badge bg-danger badge-role"><i class="bi bi-shield-lock me-1"></i>Administrator</span>
                                    @else
                                        <span class="badge bg-info text-dark badge-role"><i class="bi bi-clipboard-check me-1"></i>Auditor</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm rounded-3 px-3 py-2">
                                <i class="bi bi-box-arrow-right me-1"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    <main class="py-4 flex-grow-1">
        <div class="container">
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

    <footer class="bg-white border-top py-3 mt-auto text-center text-muted" style="font-size: 0.85rem;">
        <div class="container">
            &copy; {{ date('Y') }} <strong>Sistem Informasi Audit Internal SMKP Minerba</strong> — Kepdirjen 185
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
