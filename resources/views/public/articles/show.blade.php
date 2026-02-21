@extends('layouts.app')

@section('title', $article->title)
@section('description', Str::limit($article->abstract ?? '', 160))
@section('canonical', route('article.show', $article))

{{-- ============================================
GOOGLE SCHOLAR META TAGS (CRITICAL)
============================================ --}}
@section('scholar_meta')
    <!-- Primary Citation Meta Tags -->
    <meta name="citation_title" content="{{ $article->title }}">

    @if($article->author_name)
        <meta name="citation_author" content="{{ $article->author_name }}">
    @elseif($article->author)
        <meta name="citation_author" content="{{ $article->author->name }}">
    @endif

    @if($article->author_affiliation)
        <meta name="citation_author_institution" content="{{ $article->author_affiliation }}">
    @endif

    @if($article->author && $article->author->email)
        <meta name="citation_author_email" content="{{ $article->author->email }}">
    @endif

    <!-- Publication Information -->
    <meta name="citation_publication_date"
        content="{{ $article->published_at ? $article->published_at->format('Y/m/d') : $article->created_at->format('Y/m/d') }}">
    <meta name="citation_conference_title"
        content="{{ $article->conference->country->conference_name ?? $article->conference->title }}">
    <meta name="citation_publisher" content="International Scientific Online Conference (ISOC)">

    <!-- Abstract (Important for indexing) -->
    @if($article->abstract)
        <meta name="citation_abstract" content="{{ $article->abstract }}">
    @endif

    <!-- Keywords -->
    @if($article->keywords)
        <meta name="citation_keywords" content="{{ $article->keywords }}">
    @endif

    <!-- PDF URL (CRITICAL - Must be publicly accessible) -->
    @if($article->formatted_pdf_path)
        <meta name="citation_pdf_url" content="{{ url(Storage::url($article->formatted_pdf_path)) }}">
    @elseif($article->pdf_path)
        <meta name="citation_pdf_url" content="{{ url(Storage::url($article->pdf_path)) }}">
    @endif

    <!-- Page Numbers -->
    @if($article->start_page)
        <meta name="citation_firstpage" content="{{ $article->start_page }}">
    @endif
    @if($article->end_page)
        <meta name="citation_lastpage" content="{{ $article->end_page }}">
    @endif

    <!-- Language -->
    <meta name="citation_language" content="en">

    <!-- Public URL -->
    <meta name="citation_public_url" content="{{ route('article.show', $article) }}">

    <!-- Optional: DOI (if available) -->
    @if($article->doi)
        <meta name="citation_doi" content="{{ $article->doi }}">
    @endif
@endsection

{{-- ============================================
OPEN GRAPH META TAGS (Social Sharing)
============================================ --}}
@section('og_meta')
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $article->title }}">
    <meta property="og:description"
        content="{{ Str::limit($article->abstract ?? 'Research article from International Scientific Online Conference', 200) }}">
    <meta property="og:url" content="{{ route('article.show', $article) }}">
    @if($article->conference->country->cover_image)
        <meta property="og:image" content="{{ url($article->conference->country->cover_image) }}">
    @endif
    <meta property="article:published_time"
        content="{{ $article->published_at ? $article->published_at->toIso8601String() : $article->created_at->toIso8601String() }}">
    <meta property="article:author" content="{{ $article->author_name ?? $article->author->name ?? '' }}">
@endsection

{{-- ============================================
SCHEMA.ORG STRUCTURED DATA (SEO Boost)
============================================ --}}
@section('structured_data')
    <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "ScholarlyArticle",
                "mainEntityOfPage": {
                    "@type": "WebPage",
                    "@id": "{{ route('article.show', $article) }}"
                },
                "headline": "{{ $article->title }}",
                "author": {
                    "@type": "Person",
                    "name": "{{ $article->author_name ?? ($article->author ? $article->author->name : '') }}"
                    @if($article->author_affiliation)
                        ,"affiliation": {
                            "@type": "Organization",
                            "name": "{{ $article->author_affiliation }}"
                        }
                    @endif
                    @if($article->author && $article->author->email)
                        ,"email": "{{ $article->author->email }}"
                    @endif
                },
                @if($article->abstract)
                    "abstract": "{{ addslashes($article->abstract) }}",
                @endif
                "datePublished": "{{ $article->published_at ? $article->published_at->format('Y-m-d') : $article->created_at->format('Y-m-d') }}",
                "dateModified": "{{ $article->updated_at->format('Y-m-d') }}",
                "publisher": {
                    "@type": "Organization",
                    "name": "International Scientific Online Conference (ISOC)",
                    "url": "https://artiqle.uz",
                    "logo": {
                        "@type": "ImageObject",
                        "url": "{{ asset('images/logo.png') }}"
                    }
                },
                @if($article->keywords)
                    "keywords": "{{ $article->keywords }}",
                @endif
                "inLanguage": "en",
                "isAccessibleForFree": true,
                @if($article->page_range)
                    "pagination": "{{ $article->page_range }}",
                @endif
                "isPartOf": {
                    "@type": "PublicationEvent",
                    "name": "{{ $article->conference->title }}",
                    "location": {
                        "@type": "Place",
                        "name": "{{ $article->conference->country->name_en ?? $article->conference->country->name }}"
                    },
                    "startDate": "{{ $article->conference->conference_date->format('Y-m-d') }}"
                }
                @if($article->pdf_path)
                    ,"associatedMedia": {
                        "@type": "MediaObject",
                        "contentUrl": "{{ url(Storage::url($article->formatted_pdf_path ?? $article->pdf_path)) }}",
                        "encodingFormat": "application/pdf"
                    }
                @endif
            }
            </script>

    <!-- Breadcrumb Structured Data -->
    <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "BreadcrumbList",
                "itemListElement": [
                    {
                        "@type": "ListItem",
                        "position": 1,
                        "name": "Home",
                        "item": "{{ route('home') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "{{ $article->conference->country->name }}",
                        "item": "{{ route('country.show', $article->conference->country) }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 3,
                        "name": "{{ $article->conference->title }}",
                        "item": "{{ route('conference.show', $article->conference) }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 4,
                        "name": "{{ $article->title }}"
                    }
                ]
            }
            </script>
@endsection

{{-- ============================================
MAIN CONTENT
============================================ --}}
@section('content')
    <!-- Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" itemscope itemtype="https://schema.org/BreadcrumbList">
                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="{{ route('home') }}" itemprop="item">
                            <span itemprop="name"><i class="bi bi-house me-1"></i>Home</span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="{{ route('country.show', $article->conference->country) }}" itemprop="item">
                            <span itemprop="name">{{ $article->conference->country->name }}</span>
                        </a>
                        <meta itemprop="position" content="2" />
                    </li>
                    <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="{{ route('conference.show', $article->conference) }}" itemprop="item">
                            <span itemprop="name">{{ Str::limit($article->conference->title, 25) }}</span>
                        </a>
                        <meta itemprop="position" content="3" />
                    </li>
                    <li class="breadcrumb-item active" itemprop="itemListElement" itemscope
                        itemtype="https://schema.org/ListItem">
                        <span itemprop="name">{{ Str::limit($article->title, 30) }}</span>
                        <meta itemprop="position" content="4" />
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <div class="container py-5">
        <div class="row">
            <!-- Article Content (Main Column) -->
            <div class="col-lg-8">
                <article itemscope itemtype="https://schema.org/ScholarlyArticle">
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <!-- Article Type Badge -->
                            <div class="mb-3">
                                <span class="badge bg-primary px-3 py-2">
                                    <i class="bi bi-file-earmark-text me-1"></i>Research Article
                                </span>
                                @if($article->status === 'published')
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i>Published
                                    </span>
                                @endif
                            </div>

                            <!-- Article Title (H1 is CRITICAL for Google Scholar) -->
                            <h1 class="mb-4" itemprop="headline"
                                style="font-family: 'Roboto Slab', serif; color: var(--primary-dark); font-size: 1.75rem; line-height: 1.4;">
                                {{ $article->title }}
                            </h1>

                            <!-- Author Information (MUST be visible) -->
                            <div class="author-section mb-4 p-3"
                                style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary-blue);">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="author-avatar me-3"
                                        style="width: 50px; height: 50px; border-radius: 50%; background: var(--gradient-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.3rem;">
                                        {{ strtoupper(substr($article->author_display_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="mb-0 fs-5" itemprop="author" itemscope
                                            itemtype="https://schema.org/Person">
                                            <span
                                                itemprop="name">{{ $article->author_name ?? $article->author_display_name }}</span>
                                        </h4>
                                        @if($article->author_affiliation)
                                            <p class="text-muted mb-0 small" itemprop="affiliation">
                                                <i class="bi bi-building me-1"></i>{{ $article->author_affiliation }}
                                            </p>
                                        @endif
                                        @if($article->author && $article->author->email)
                                            <p class="text-muted mb-0 small">
                                                <i class="bi bi-envelope me-1"></i>
                                                <a href="mailto:{{ $article->author->email }}" itemprop="email"
                                                    class="text-decoration-none">
                                                    {{ $article->author->email }}
                                                </a>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Publication Metadata (MUST be visible) -->
                            <div class="d-flex flex-wrap gap-3 mb-4">
                                @if($article->published_at)
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="bi bi-calendar-fill me-1 text-success"></i>
                                        <time datetime="{{ $article->published_at->toIso8601String() }}"
                                            itemprop="datePublished">
                                            {{ $article->published_at->format('F d, Y') }}
                                        </time>
                                    </span>
                                @endif
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <i class="bi bi-file-earmark-fill me-1 text-warning"></i>
                                    Pages: <span itemprop="pagination">{{ $article->page_range }}</span>
                                    ({{ $article->page_count }} pages)
                                </span>
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <i class="bi bi-globe me-1 text-info"></i>
                                    {{ $article->conference->country->name_en ?? $article->conference->country->name }}
                                </span>
                            </div>

                            <!-- Abstract Section (CRITICAL - Must be plain text, NOT image) -->
                            @if($article->abstract)
                                <section class="abstract-section mb-4" id="abstract">
                                    <div class="p-4"
                                        style="background: var(--light-blue); border-radius: 10px; border-left: 4px solid var(--primary-blue);">
                                        <h2 class="h5 fw-bold mb-3" style="color: var(--primary-dark);">
                                            <i class="bi bi-text-paragraph me-2"></i>Abstract
                                        </h2>
                                        <p class="mb-0" itemprop="abstract" style="line-height: 1.8; text-align: justify;">
                                            {{ $article->abstract }}
                                        </p>
                                    </div>
                                </section>
                            @endif

                            <!-- Keywords Section -->
                            @if($article->keywords)
                                <section class="keywords-section mb-4" id="keywords">
                                    <h3 class="h6 fw-bold mb-2" style="color: var(--primary-dark);">
                                        <i class="bi bi-tags me-2"></i>Keywords
                                    </h3>
                                    <div itemprop="keywords">
                                        @foreach(explode(',', $article->keywords) as $keyword)
                                            <span class="badge bg-light text-dark border me-1 mb-1">{{ trim($keyword) }}</span>
                                        @endforeach
                                    </div>
                                </section>
                            @endif

                            <!-- PDF Viewer Section -->
                            <section class="pdf-section mb-4" id="pdf-viewer">
                                <h3 class="h5 fw-bold mb-3" style="color: var(--primary-dark);">
                                    <i class="bi bi-file-pdf me-2 text-danger"></i>Full Article
                                </h3>
                                <div class="ratio"
                                    style="--bs-aspect-ratio: 130%; border-radius: 10px; overflow: hidden; box-shadow: var(--shadow-md);">
                                    <iframe src="{{ Storage::url($article->formatted_pdf_path ?? $article->pdf_path) }}"
                                        frameborder="0" style="background: #f5f5f5;"
                                        title="Article PDF: {{ $article->title }}" loading="lazy">
                                    </iframe>
                                </div>
                            </section>

                            <!-- Download Buttons -->
                            <section class="download-section">
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ Storage::url($article->formatted_pdf_path ?? $article->pdf_path) }}"
                                        class="btn btn-primary btn-lg" target="_blank" itemprop="url" rel="noopener"
                                        download>
                                        <i class="bi bi-download me-2"></i>Download PDF
                                    </a>
                                    <button type="button" class="btn btn-info btn-lg" onclick="copyArticleLink()"
                                        id="copyLinkBtn">
                                        <i class="bi bi-link-45deg me-2"></i>Maqola havolasi
                                    </button>
                                    @auth
                                        @if(auth()->user()->role === 'admin' && $article->certificate)
                                            <a href="{{ Storage::url($article->certificate->pdf_path) }}"
                                                class="btn btn-success btn-lg" target="_blank">
                                                <i class="bi bi-award me-2"></i>Download Certificate
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            </section>
                        </div>
                    </div>

                    <!-- Citation Section -->
                    <div class="card mb-4">
                        <div class="card-header" style="background: var(--light-blue);">
                            <h3 class="h6 mb-0"><i class="bi bi-quote me-2"></i>How to Cite</h3>
                        </div>
                        <div class="card-body">
                            <div class="citation-box p-3"
                                style="background: #f8f9fa; border-radius: 8px; font-family: 'Courier New', monospace; font-size: 0.9rem;">
                                {{ $article->author_name ?? $article->author_display_name }}
                                ({{ $article->published_at ? $article->published_at->format('Y') : date('Y') }}).
                                {{ $article->title }}.
                                <em>{{ $article->conference->country->conference_name ?? $article->conference->title }}</em>,
                                {{ $article->page_range }}.
                                {{ $article->conference->country->name_en ?? $article->conference->country->name }}.
                                @if($article->doi)
                                    https://doi.org/{{ $article->doi }}
                                @endif
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-secondary" onclick="copyCitation()">
                                    <i class="bi bi-clipboard me-1"></i>Copy Citation
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Conference Info -->
                <div class="card mb-4">
                    <div class="card-header" style="background: var(--gradient-blue); color: white;">
                        <i class="bi bi-journal-bookmark me-1"></i>Conference
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            @if($article->conference->country->flag_url)
                                <img src="{{ Storage::url($article->conference->country->flag_url) }}"
                                    alt="{{ $article->conference->country->name }}"
                                    style="width: 50px; height: 34px; object-fit: cover; border-radius: 4px; margin-right: 12px;">
                            @endif
                            <div>
                                <h6 class="mb-1">
                                    <a href="{{ route('conference.show', $article->conference) }}"
                                        class="text-decoration-none" style="color: var(--primary-blue);">
                                        {{ $article->conference->title }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $article->conference->country->name }}
                                </p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span><i
                                    class="bi bi-calendar me-1"></i>{{ $article->conference->conference_date->format('F d, Y') }}</span>
                            <span><i class="bi bi-file-text me-1"></i>{{ $article->conference->articles->count() }}
                                articles</span>
                        </div>
                    </div>
                </div>

                <!-- Article Metrics -->
                <div class="card mb-4">
                    <div class="card-header"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <i class="bi bi-graph-up me-1"></i>Article Info
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Article Number</span>
                                <strong>{{ $article->order_number }}</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Pages</span>
                                <strong>{{ $article->page_range }}</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Published</span>
                                <strong>{{ $article->published_at ? $article->published_at->format('M d, Y') : 'Pending' }}</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <span class="text-muted">Language</span>
                                <strong>English</strong>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Author Card -->
                <div class="card mb-4">
                    <div class="card-header"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                        <i class="bi bi-person me-1"></i>Author
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center justify-content-center me-3"
                                style="width: 50px; height: 50px; border-radius: 50%; background: var(--gradient-blue); color: white; font-weight: 700; font-size: 1.2rem;">
                                {{ strtoupper(substr($article->author_display_name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $article->author_display_name }}</h6>
                                @if($article->author_affiliation)
                                    <small class="text-muted">{{ $article->author_affiliation }}</small>
                                @elseif($article->author)
                                    <small class="text-muted">{{ $article->author->email }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Certificate (Admin Only) --}}
                {{-- Certificate (Admin Only) --}}
                @auth
                    @if(auth()->user()->role === 'admin' && $article->certificate)
                        <div class="card mb-4">
                            <div class="card-header"
                                style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                                <i class="bi bi-award me-1"></i>Certificate (Admin)
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="bi bi-award-fill" style="font-size: 4rem; color: var(--accent-orange);"></i>
                                </div>
                                <h6 class="mb-2">{{ $article->certificate->certificate_number }}</h6>
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-calendar-check me-1"></i>Issued:
                                    {{ $article->certificate->issue_date->format('M d, Y') }}
                                </p>
                                <a href="{{ Storage::url($article->certificate->pdf_path) }}" class="btn btn-warning btn-sm w-100"
                                    target="_blank">
                                    <i class="bi bi-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                    @endif
                @endauth
                <!-- Share Buttons -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="bi bi-share me-2"></i>Share This Article</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}"
                                class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                                <i class="bi bi-telegram"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}"
                                class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                                class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-secondary"
                                onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Link copied!');">
                                <i class="bi bi-link-45deg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function copyCitation() {
            const citation = `{{ $article->author_name ?? $article->author_display_name }} ({{ $article->published_at ? $article->published_at->format('Y') : date('Y') }}). {{ $article->title }}. {{ $article->conference->country->conference_name ?? $article->conference->title }}, {{ $article->page_range }}. {{ $article->conference->country->name_en ?? $article->conference->country->name }}.`;
            navigator.clipboard.writeText(citation).then(() => {
                alert('Citation copied to clipboard!');
            });
        }

        function copyArticleLink() {
            const articleUrl = '{{ route('article.show', $article) }}';
            navigator.clipboard.writeText(articleUrl).then(() => {
                const btn = document.getElementById('copyLinkBtn');
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Nusxalandi!';
                btn.classList.remove('btn-info');
                btn.classList.add('btn-success');

                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-info');
                }, 2000);
            }).catch(err => {
                // Agar clipboard ishlamasa, prompt yordamida ko'rsatamiz
                prompt('Maqola havolasi:', articleUrl);
            });
        }
    </script>
@endpush