@extends('layouts.app')

@section('title', 'Mening sertifikatlarim')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">
                <i class="bi bi-award text-primary me-2"></i>Mening sertifikatlarim
            </h1>
            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Dashboard
            </a>
        </div>

        @if($articles->count() > 0)
            <div class="row g-4">
                @foreach($articles as $article)
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-award text-warning" style="font-size: 4rem;"></i>
                                <h5 class="mt-3">{{ $article->certificate->certificate_number }}</h5>
                                <p class="text-muted">{{ $article->title }}</p>
                                <p class="small text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $article->conference->country->name }}
                                    <br>
                                    <i class="bi bi-calendar me-1"></i>{{ $article->certificate->issue_date->format('d.m.Y') }}
                                </p>
                                <a href="{{ route('user.certificate.download', $article) }}" class="btn btn-success">
                                    <i class="bi bi-download me-1"></i>Yuklab olish
                                </a>
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
                <i class="bi bi-award text-muted" style="font-size: 5rem;"></i>
                <h4 class="mt-3 text-muted">Hozircha sertifikatlar yo'q</h4>
                <p class="text-muted">Maqolangiz nashr etilganda sertifikat olasiz.</p>
            </div>
        @endif
    </div>
@endsection