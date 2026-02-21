<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="@yield('description', 'International Scientific Conferences - Xalqaro Ilmiy Konferensiyalar Platformasi')">

    <!-- Canonical URL -->
    @hasSection('canonical')
        <link rel="canonical" href="@yield('canonical')">
    @endif

    <!-- Google Scholar Meta Tags -->
    @yield('scholar_meta')

    <!-- Open Graph Meta Tags -->
    <meta property="og:site_name" content="ISOC - International Scientific Online Conference">
    <meta property="og:locale" content="en_US">
    @yield('og_meta')

    <!-- Structured Data (Schema.org) -->
    @yield('structured_data')

    <title>@yield('title', 'ISC') - International Scientific Conferences</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Roboto+Slab:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-blue: #1a4b8c;
            --primary-dark: #0d2d5a;
            --accent-orange: #f5a623;
            --accent-teal: #00bcd4;
            --light-blue: #e8f4fc;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --white: #ffffff;
            --gradient-blue: linear-gradient(135deg, #1a4b8c 0%, #2d7dd2 100%);
            --gradient-header: linear-gradient(90deg, #1a4b8c 0%, #0d2d5a 100%);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.15);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Header/Navbar */
        .main-header {
            background: var(--white);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .top-bar {
            background: var(--gradient-header);
            color: var(--white);
            padding: 8px 0;
            font-size: 0.875rem;
        }

        .top-bar a {
            color: var(--white);
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .top-bar a:hover {
            opacity: 0.8;
        }

        .navbar {
            padding: 10px 0;
            background: var(--white) !important;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            max-height: 60px;
            transition: transform 0.3s;
        }

        .navbar-brand:hover img {
            transform: scale(1.02);
        }

        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            padding: 10px 18px !important;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .nav-link:hover {
            background: var(--light-blue);
            color: var(--primary-blue) !important;
        }

        .nav-link.active {
            background: var(--primary-blue);
            color: var(--white) !important;
        }

        .btn-login {
            background: var(--gradient-blue);
            color: var(--white) !important;
            border: none;
            padding: 10px 24px !important;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--light-blue) 0%, #fff 50%, var(--light-blue) 100%);
            position: relative;
            padding: 60px 0 80px;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%231a4b8c' fill-opacity='0.03'%3E%3Ccircle cx='50' cy='50' r='2'/%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .hero-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 1200 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,50 C300,100 600,0 900,50 C1050,75 1150,50 1200,50 L1200,100 L0,100 Z' fill='%231a4b8c' opacity='0.1'/%3E%3Cpath d='M0,60 C250,100 500,20 750,60 C900,80 1100,40 1200,60 L1200,100 L0,100 Z' fill='%2300bcd4' opacity='0.1'/%3E%3C/svg%3E");
            background-size: cover;
        }

        .hero-logo {
            max-width: 450px;
            width: 100%;
            margin-bottom: 30px;
            animation: fadeInUp 0.8s ease-out;
        }

        .hero-title {
            font-family: 'Roboto Slab', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 20px;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-light);
            max-width: 700px;
            margin: 0 auto 30px;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            overflow: hidden;
            background: var(--white);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .journal-card {
            border-left: 4px solid var(--primary-blue);
        }

        .journal-card .card-header {
            background: var(--light-blue);
            border-bottom: none;
            padding: 20px;
        }

        .journal-card .card-title {
            color: var(--primary-blue);
            font-weight: 600;
            margin: 0;
            font-size: 1.1rem;
        }

        .journal-card .card-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s;
        }

        .journal-card .card-title a:hover {
            color: var(--accent-teal);
        }

        .journal-card .card-body {
            padding: 20px;
        }

        .journal-card .card-text {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        /* Conference Card */
        .conference-card {
            position: relative;
        }

        .conference-card .card-img-top {
            height: 180px;
            object-fit: cover;
        }

        .conference-card .country-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.95);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            box-shadow: var(--shadow-sm);
        }

        .conference-card .country-badge img {
            width: 20px;
            height: 14px;
            margin-right: 6px;
            border-radius: 2px;
        }

        /* Buttons */
        .btn-primary {
            background: var(--gradient-blue);
            border: none;
            padding: 12px 28px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(26, 75, 140, 0.4);
            background: var(--gradient-blue);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            border-radius: 30px;
            padding: 10px 26px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-primary:hover {
            background: var(--primary-blue);
            color: var(--white);
            transform: translateY(-2px);
        }

        .btn-view {
            background: var(--primary-blue);
            color: var(--white);
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-view:hover {
            background: var(--primary-dark);
            color: var(--white);
        }

        .btn-current {
            background: transparent;
            border: 1px solid var(--primary-blue);
            color: var(--primary-blue);
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-current:hover {
            background: var(--light-blue);
            color: var(--primary-blue);
        }

        /* Section Styles */
        .section-title {
            font-family: 'Roboto Slab', serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: var(--accent-orange);
            border-radius: 2px;
        }

        .section-title.text-center::after {
            left: 50%;
            transform: translateX(-50%);
        }

        /* Stats Section */
        .stats-section {
            background: var(--gradient-header);
            padding: 50px 0;
            margin: 0;
        }

        .stat-item {
            text-align: center;
            color: var(--white);
            padding: 20px;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 5px;
            background: linear-gradient(135deg, #fff 0%, #e0e0e0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* Countries Grid */
        .country-card {
            text-align: center;
            padding: 25px 20px;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .country-card:hover {
            background: var(--light-blue);
        }

        .country-flag {
            width: 80px;
            height: 54px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 15px;
        }

        .country-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
        }

        .country-count {
            color: var(--primary-blue);
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Book Cover Styles */
        .book-cover {
            position: relative;
            display: inline-block;
            perspective: 1000px;
        }

        .book-cover img {
            max-width: 100%;
            border-radius: 8px;
            box-shadow:
                10px 10px 30px rgba(0, 0, 0, 0.2),
                0 0 5px rgba(0, 0, 0, 0.1);
            transform: rotateY(-15deg) rotateX(5deg);
            transition: transform 0.4s ease;
        }

        .book-cover:hover img {
            transform: rotateY(-5deg) rotateX(2deg);
        }

        .book-cover::before {
            content: '';
            position: absolute;
            left: 0;
            top: 5%;
            width: 20px;
            height: 90%;
            background: linear-gradient(to right,
                    rgba(0, 0, 0, 0.2) 0%,
                    rgba(0, 0, 0, 0.05) 50%,
                    transparent 100%);
            transform: rotateY(-15deg);
            border-radius: 3px 0 0 3px;
        }

        .country-cover-wrapper {
            max-width: 280px;
            margin: 0 auto;
        }

        .country-cover-wrapper img {
            width: 100%;
            border-radius: 8px;
            box-shadow:
                8px 8px 25px rgba(0, 0, 0, 0.2),
                0 2px 5px rgba(0, 0, 0, 0.1);
            transform: perspective(800px) rotateY(-10deg);
            transition: all 0.4s ease;
        }

        .country-cover-wrapper:hover img {
            transform: perspective(800px) rotateY(-5deg);
            box-shadow:
                12px 12px 35px rgba(0, 0, 0, 0.25),
                0 3px 8px rgba(0, 0, 0, 0.15);
        }

        /* Footer */
        .footer {
            background: var(--gradient-header);
            color: var(--white);
            padding: 60px 0 30px;
        }

        .footer h5 {
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .footer p,
        .footer a {
            color: rgba(255, 255, 255, 0.8);
        }

        .footer a {
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: var(--white);
        }

        .footer ul {
            list-style: none;
            padding: 0;
        }

        .footer ul li {
            margin-bottom: 10px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 25px;
            margin-top: 40px;
            text-align: center;
        }

        /* Badges */
        .badge-primary {
            background: var(--primary-blue);
            color: var(--white);
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 20px;
        }

        .badge-success {
            background: #10b981;
        }

        .badge-warning {
            background: var(--accent-orange);
        }

        /* Breadcrumb */
        .breadcrumb-section {
            background: var(--light-blue);
            padding: 20px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .breadcrumb {
            margin: 0;
            padding: 0;
            background: transparent;
        }

        .breadcrumb-item a {
            color: var(--primary-blue);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--text-light);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-logo {
                max-width: 300px;
            }

            .hero-title {
                font-size: 1.75rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .section-title {
                font-size: 1.5rem;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Article Card */
        .article-card {
            border-left: 4px solid var(--accent-teal);
        }

        .article-card .article-title {
            color: var(--primary-blue);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }

        .article-card .article-title:hover {
            color: var(--accent-teal);
        }

        .article-meta {
            font-size: 0.875rem;
            color: var(--text-light);
        }

        /* Language Dropdown */
        .language-dropdown .dropdown-toggle {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--white);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.875rem;
        }

        .language-dropdown .dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span><i class="bi bi-envelope me-2"></i>info@isc-conferences.org</span>
                    <span class="ms-4"><i class="bi bi-telephone me-2"></i>+998 90 123 45 67</span>
                </div>
                <div class="col-md-6 text-end">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="me-3">
                                <i class="bi bi-gear me-1"></i>Admin Panel
                            </a>
                        @endif
                        <a href="{{ route('user.dashboard') }}" class="me-3">
                            <i class="bi bi-person me-1"></i>{{ auth()->user()->name }}
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 text-white text-decoration-none">
                                <i class="bi bi-box-arrow-right me-1"></i>Chiqish
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i>Kirish</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="ISC - International Scientific Conferences">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                                href="{{ route('home') }}">
                                <i class="bi bi-house-door me-1"></i>Bosh sahifa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('countries*') ? 'active' : '' }}"
                                href="{{ route('countries') }}">
                                <i class="bi bi-globe me-1"></i>Davlatlar
                            </a>
                        </li>
                        @guest
                            <li class="nav-item ms-2">
                                <a class="nav-link btn-login" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Kirish
                                </a>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="ISC"
                        style="max-height: 80px; margin-bottom: 20px; filter: brightness(0) invert(1);">
                    <p>International Scientific Conferences - Xalqaro ilmiy konferensiyalar platformasi. Turli davlatlar
                        bo'yicha online ilmiy maqola konferensiyalarida ishtirok eting.</p>
                </div>
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5>Havolalar</h5>
                    <ul>
                        <li><a href="{{ route('home') }}">Bosh sahifa</a></li>
                        <li><a href="{{ route('countries') }}">Davlatlar</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <h5>Foydalanuvchilar uchun</h5>
                    <ul>
                        <li><a href="{{ route('login') }}">Kirish</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <h5>Aloqa</h5>
                    <p>
                        <i class="bi bi-envelope me-2"></i>info@isc-conferences.org<br>
                        <i class="bi bi-telephone me-2"></i>+998 90 123 45 67<br>
                        <i class="bi bi-geo-alt me-2"></i>Toshkent, O'zbekiston
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="mb-0">&copy; {{ date('Y') }} International Scientific Conferences. Barcha huquqlar
                    himoyalangan.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>