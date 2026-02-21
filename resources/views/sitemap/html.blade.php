@extends('layouts.app')

@section('title', 'Sitemap')
@section('description', 'Complete sitemap of all countries, conferences, and articles in the International Scientific Online Conference platform.')

@section('content')
    <div class="container py-5">
        <h1 class="section-title text-center mb-5">
            <i class="bi bi-map me-2"></i>Sitemap
        </h1>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        {{-- Quick Stats --}}
                        <div class="row text-center mb-5">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="p-3 rounded" style="background: var(--light-blue);">
                                    <h2 class="display-5 fw-bold" style="color: var(--primary-blue);">
                                        {{ $countries->count() }}
                                    </h2>
                                    <p class="mb-0 text-muted">Countries</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="p-3 rounded" style="background: #dcfce7;">
                                    <h2 class="display-5 fw-bold text-success">
                                        {{ $countries->sum(fn($c) => $c->conferences->count()) }}
                                    </h2>
                                    <p class="mb-0 text-muted">Conferences</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded" style="background: #fef3c7;">
                                    <h2 class="display-5 fw-bold" style="color: #d97706;">
                                        {{ $countries->sum(fn($c) => $c->conferences->sum(fn($conf) => $conf->articles->count())) }}
                                    </h2>
                                    <p class="mb-0 text-muted">Articles</p>
                                </div>
                            </div>
                        </div>

                        {{-- Countries List --}}
                        @foreach($countries as $country)
                            <div class="mb-4">
                                <h2 class="h4 mb-3 d-flex align-items-center">
                                    @if($country->flag_url)
                                        <img src="{{ Storage::url($country->flag_url) }}" alt="{{ $country->name }}"
                                            style="width: 32px; height: 22px; object-fit: cover; border-radius: 3px; margin-right: 10px;">
                                    @endif
                                    <a href="{{ route('country.show', $country) }}" class="text-decoration-none"
                                        style="color: var(--primary-blue);">
                                        {{ $country->name_en ?? $country->name }}
                                    </a>
                                </h2>

                                @foreach($country->conferences as $conference)
                                    <div class="ms-4 mb-3">
                                        <h3 class="h5 mb-2">
                                            <i class="bi bi-journal-bookmark me-2 text-muted"></i>
                                            <a href="{{ route('conference.show', $conference) }}" class="text-decoration-none">
                                                {{ $conference->title }}
                                            </a>
                                            <small class="text-muted">({{ $conference->conference_date->format('M Y') }})</small>
                                        </h3>

                                        @if($conference->articles->count() > 0)
                                            <ul class="list-unstyled ms-4">
                                                @foreach($conference->articles as $article)
                                                    <li class="mb-2">
                                                        <i class="bi bi-file-earmark-text me-2 text-muted"></i>
                                                        <a href="{{ route('article.show', $article) }}" class="text-decoration-none">
                                                            {{ Str::limit($article->title, 80) }}
                                                        </a>
                                                        <small class="text-muted">
                                                            - {{ $article->author_display_name }}
                                                            @if($article->page_range)
                                                                (pp. {{ $article->page_range }})
                                                            @endif
                                                        </small>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted ms-4"><small>No articles published yet.</small></p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if(!$loop->last)
                                <hr class="my-4">
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- XML Sitemap Links --}}
                <div class="card mt-4">
                    <div class="card-body">
                        <h3 class="h5 mb-3"><i class="bi bi-code-slash me-2"></i>Machine-Readable Sitemaps</h3>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <a href="{{ url('/sitemap.xml') }}" target="_blank" class="text-decoration-none">
                                    <i class="bi bi-file-code me-2"></i>sitemap.xml
                                </a>
                                <span class="text-muted">- Complete XML sitemap</span>
                            </li>
                            <li>
                                <a href="{{ url('/sitemap-articles.xml') }}" target="_blank" class="text-decoration-none">
                                    <i class="bi bi-file-code me-2"></i>sitemap-articles.xml
                                </a>
                                <span class="text-muted">- Articles only (for Google Scholar)</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection