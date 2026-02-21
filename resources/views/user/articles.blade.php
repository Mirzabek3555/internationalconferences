@extends('layouts.app')

@section('title', 'Mening maqolalarim')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">
                <i class="bi bi-file-text text-primary me-2"></i>Mening maqolalarim
            </h1>
            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Dashboard
            </a>
        </div>

        @if($articles->count() > 0)
            <div class="row g-4">
                @foreach($articles as $article)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-1">{{ $article->title }}</h5>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-calendar-event me-1"></i>{{ $article->conference->title }}
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-geo-alt me-1"></i>{{ $article->conference->country->name }}
                                        </p>
                                        <div>
                                            @if($article->status === 'published')
                                                <span class="badge bg-success">Nashr etilgan</span>
                                            @else
                                                <span class="badge bg-warning">Kutilmoqda</span>
                                            @endif
                                            <span class="badge bg-secondary ms-1">{{ $article->page_range }}</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <a href="{{ route('user.article.download', $article) }}" class="btn btn-primary">
                                            <i class="bi bi-download me-1"></i>PDF
                                        </a>
                                        @if($article->certificate)
                                            <a href="{{ route('user.certificate.download', $article) }}" class="btn btn-success">
                                                <i class="bi bi-award me-1"></i>Sertifikat
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $articles->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-file-x text-muted" style="font-size: 5rem;"></i>
                <h4 class="mt-3 text-muted">Hozircha maqolalar yo'q</h4>
                <p class="text-muted">Sizning maqolalaringiz admin tomonidan joylanganda bu yerda ko'rinadi.</p>
            </div>
        @endif
    </div>
@endsection