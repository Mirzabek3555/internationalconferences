<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Artiqle Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
        }

        body {
            min-height: 100vh;
            background: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 20px;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav .nav-link i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }

        .topbar {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #eee;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-journal-richtext me-2"></i>Artiqle
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.countries.index') }}"
                class="nav-link {{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
                <i class="bi bi-globe"></i>Davlatlar
            </a>
            <a href="{{ route('admin.conferences.index') }}"
                class="nav-link {{ request()->routeIs('admin.conferences.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i>Konferensiyalar
            </a>
            <a href="{{ route('admin.articles.index') }}"
                class="nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}">
                <i class="bi bi-file-text"></i>Maqolalar
            </a>
            <hr class="my-3" style="border-color: rgba(255,255,255,0.1);">
            <a href="{{ route('home') }}" class="nav-link">
                <i class="bi bi-house"></i>Saytga qaytish
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link w-100 border-0 bg-transparent text-start">
                    <i class="bi bi-box-arrow-right"></i>Chiqish
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <h4 class="mb-0">@yield('page-title', 'Admin Panel')</h4>
            <div>
                <span class="text-muted me-2">{{ auth()->user()->name }}</span>
                <span class="badge bg-primary">Admin</span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>