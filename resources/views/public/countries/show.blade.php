@extends('layouts.app')

@section('title', $country->conference_name ?? $country->name)

@section('content')
    <!-- Journal Header -->
    <section class="journal-header py-3" style="background: #fff; border-bottom: 3px solid #1a5276;">
        <div class="container">
            <h1 class="journal-title mb-2" style="color: #1a5276; font-size: 1.8rem; font-weight: 600; line-height: 1.3;">
                {{ $country->conference_name ?? 'International Scientific Conference Proceedings' }}
            </h1>
            <!-- Navigation -->
            <nav class="journal-nav">
                <a href="#current" class="journal-nav-link active">Current</a>
                <a href="{{ route('archive') }}" class="journal-nav-link">Archives</a>
                <a href="#" class="journal-nav-link">About</a>
            </nav>
        </div>
    </section>

    <div class="container py-4">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Current Issue -->
                <section id="current" class="mb-5">
                    <h2 class="section-heading">Current Issue</h2>

                    <div class="issue-info mb-4">
                        <h3 class="issue-title" style="color: #1a5276; font-size: 1.1rem; font-weight: 600;">
                            Vol. {{ date('Y') }} No. {{ date('m') }} ({{ date('Y') }}):
                            {{ $country->conference_name ?? $country->name . ' - Scientific Conference Proceedings' }}
                        </h3>

                        <div class="row mt-4">
                            <!-- Cover Image -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                @if($country->cover_image)
                                    <img src="{{ asset($country->cover_image) }}" alt="{{ $country->name }}"
                                        class="img-fluid shadow-sm" style="max-height: 280px; border-radius: 4px;">
                                @else
                                    <div class="cover-placeholder d-flex align-items-center justify-content-center"
                                        style="height: 280px; background: linear-gradient(135deg, #1a5276, #2980b9); border-radius: 4px; color: #fff;">
                                        <div class="text-center p-3">
                                            @if($country->flag_url)
                                                <img src="{{ Storage::url($country->flag_url) }}"
                                                    style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px; margin-bottom: 15px;">
                                            @endif
                                            <h4 style="font-size: 1.2rem;">{{ $country->name }}</h4>
                                            <p style="font-size: 0.85rem; opacity: 0.8;">{{ $country->name_en }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <!-- Description -->
                            <div class="col-md-8">
                                <p class="text-muted" style="line-height: 1.7;">
                                    @if($country->conference_description)
                                        {{ $country->conference_description }}
                                    @else
                                        The Proceedings of the scientific conference on multidisciplinary research are an
                                        electronic conference series.
                                        The materials of this conference publish the original research work presented by the
                                        conference participants.
                                    @endif
                                </p>
                                <p class="mt-3">
                                    <strong>Published:</strong> {{ now()->format('Y-m-d') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Articles Section -->
                <section id="articles" class="articles-section">
                    <fieldset class="articles-fieldset">
                        <legend>Articles</legend>

                        @forelse($articles as $article)
                            <div class="article-item py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <h4 class="article-title">
                                    <a href="{{ route('article.show', $article) }}">
                                        {{ strtoupper($article->title) }}
                                    </a>
                                </h4>
                                <p class="article-authors text-muted mb-2">
                                    {{ $article->author_name ?? $article->author_display_name }}
                                    @if($article->author_affiliation)
                                        <br><small class="text-secondary">{{ $article->author_affiliation }}</small>
                                    @endif
                                </p>
                                <div class="d-flex align-items-center gap-3">
                                    @if($article->formatted_pdf_path || $article->pdf_path)
                                        <a href="{{ Storage::url($article->formatted_pdf_path ?? $article->pdf_path) }}"
                                            class="btn btn-outline-secondary btn-sm" target="_blank">
                                            <i class="bi bi-file-pdf me-1"></i>download
                                        </a>
                                    @endif
                                    <span class="text-muted ms-auto">{{ $article->page_range }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-file-earmark-x display-4 text-muted"></i>
                                <p class="mt-3 text-muted">No articles published yet.</p>
                            </div>
                        @endforelse
                    </fieldset>
                </section>

                <!-- Pagination -->
                @if($articles->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $articles->links() }}
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Information -->
                <div class="sidebar-block mb-4">
                    <h5 class="sidebar-title">Information</h5>
                    <ul class="sidebar-links">
                        <li><a href="#"><i class="bi bi-book me-2"></i>For Readers</a></li>
                        <li><a href="#"><i class="bi bi-pencil me-2"></i>For Authors</a></li>
                        <li><a href="#"><i class="bi bi-building me-2"></i>For Librarians</a></li>
                    </ul>
                </div>

                <!-- Country Info -->
                <div class="sidebar-block mb-4">
                    <h5 class="sidebar-title">Conference Country</h5>
                    <div class="country-info text-center py-3">
                        @if($country->flag_url)
                            <img src="{{ Storage::url($country->flag_url) }}" alt="{{ $country->name }}"
                                style="width: 100px; height: 65px; object-fit: cover; border-radius: 6px; box-shadow: 0 3px 10px rgba(0,0,0,0.15);">
                        @endif
                        <h6 class="mt-3 mb-1">{{ $country->name }}</h6>
                        <p class="text-muted small mb-0">{{ $country->name_en }}</p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="sidebar-block mb-4">
                    <h5 class="sidebar-title">Statistics</h5>
                    <ul class="stats-list">
                        <li>
                            <span class="stat-label"><i class="bi bi-file-earmark-text me-2"></i>Total Articles</span>
                            <span class="stat-value">{{ $articles->total() }}</span>
                        </li>
                        <li>
                            <span class="stat-label"><i class="bi bi-calendar-check me-2"></i>Published</span>
                            <span class="stat-value">{{ date('Y') }}</span>
                        </li>
                        <li>
                            <span class="stat-label"><i class="bi bi-award me-2"></i>Certificate</span>
                            <span class="stat-value text-success">Available</span>
                        </li>
                    </ul>
                </div>

                <!-- QR Code -->
                @if($country->cover_image)
                    <div class="sidebar-block">
                        <h5 class="sidebar-title">Quick Access</h5>
                        <div class="text-center py-3">
                            <div class="qr-placeholder mx-auto"
                                style="width: 120px; height: 120px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-qr-code" style="font-size: 3rem; color: #999;"></i>
                            </div>
                            <p class="text-muted small mt-2 mb-0">Scan to access</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Journal Header */
        .journal-title {
            margin: 0;
        }

        .journal-nav {
            display: flex;
            gap: 5px;
            margin-top: 15px;
        }

        .journal-nav-link {
            padding: 8px 16px;
            text-decoration: none;
            color: #555;
            font-size: 0.9rem;
            border-radius: 4px 4px 0 0;
            transition: all 0.2s;
        }

        .journal-nav-link:hover,
        .journal-nav-link.active {
            background: #1a5276;
            color: #fff;
        }

        /* Section Heading */
        .section-heading {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        /* Articles Fieldset */
        .articles-fieldset {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            margin: 0;
        }

        .articles-fieldset legend {
            font-size: 1rem;
            font-weight: 600;
            color: #555;
            padding: 0 10px;
            width: auto;
            margin-bottom: 0;
        }

        /* Article Item */
        .article-item {
            transition: background 0.2s;
        }

        .article-item:hover {
            background: #fafafa;
        }

        .article-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .article-title a {
            color: #1a5276;
            text-decoration: none;
        }

        .article-title a:hover {
            text-decoration: underline;
        }

        .article-authors {
            font-size: 0.88rem;
        }

        /* Sidebar */
        .sidebar-block {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: hidden;
        }

        .sidebar-title {
            background: #f8f9fa;
            padding: 12px 15px;
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: #1a5276;
            border-bottom: 1px solid #e0e0e0;
        }

        .sidebar-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-links li {
            border-bottom: 1px solid #f0f0f0;
        }

        .sidebar-links li:last-child {
            border-bottom: none;
        }

        .sidebar-links a {
            display: block;
            padding: 10px 15px;
            color: #1a5276;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .sidebar-links a:hover {
            background: #f8f9fa;
        }

        /* Stats List */
        .stats-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .stats-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.88rem;
        }

        .stats-list li:last-child {
            border-bottom: none;
        }

        .stat-label {
            color: #666;
        }

        .stat-value {
            font-weight: 600;
            color: #333;
        }

        /* Breadcrumb Section */
        .breadcrumb-section {
            background: #f8f9fa;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
    </style>
@endsection