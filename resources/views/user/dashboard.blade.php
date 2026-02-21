@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">
                <i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard
            </h1>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['total_articles'] }}</h3>
                                <p class="mb-0 text-white-50">Jami maqolalar</p>
                            </div>
                            <i class="bi bi-file-text" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['published_articles'] }}</h3>
                                <p class="mb-0 text-white-50">Nashr etilgan</p>
                            </div>
                            <i class="bi bi-check-circle" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $stats['pending_articles'] }}</h3>
                                <p class="mb-0 text-white-50">Kutilmoqda</p>
                            </div>
                            <i class="bi bi-clock" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <a href="{{ route('user.articles') }}" class="text-decoration-none">
                    <div class="card h-100">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-file-text text-primary" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">Mening maqolalarim</h5>
                            <p class="text-muted mb-0">Barcha maqolalaringizni ko'ring</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('user.certificates') }}" class="text-decoration-none">
                    <div class="card h-100">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-award text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">Mening sertifikatlarim</h5>
                            <p class="text-muted mb-0">Sertifikatlaringizni yuklab oling</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Articles -->
        <h3 class="fw-bold mb-4">
            <i class="bi bi-clock-history text-primary me-2"></i>So'nggi maqolalar
        </h3>

        @if($recentArticles->count() > 0)
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Maqola</th>
                                <th>Konferensiya</th>
                                <th>Status</th>
                                <th>Sana</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentArticles as $article)
                                <tr>
                                    <td>{{ Str::limit($article->title, 40) }}</td>
                                    <td>
                                        <span class="text-muted">{{ Str::limit($article->conference->title, 30) }}</span>
                                    </td>
                                    <td>
                                        @if($article->status === 'published')
                                            <span class="badge bg-success">Nashr etilgan</span>
                                        @else
                                            <span class="badge bg-warning">Kutilmoqda</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $article->created_at->format('d.m.Y') }}</td>
                                    <td>
                                        <a href="{{ route('user.article.download', $article) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @if($article->certificate)
                                            <a href="{{ route('user.certificate.download', $article) }}"
                                                class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-award"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-file-x text-muted" style="font-size: 5rem;"></i>
                <h4 class="mt-3 text-muted">Hozircha maqolalar yo'q</h4>
            </div>
        @endif
    </div>
@endsection