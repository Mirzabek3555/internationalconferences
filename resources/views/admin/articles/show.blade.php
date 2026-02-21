@extends('layouts.admin')

@section('page-title', $article->title)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-file-text me-2"></i>Maqola ma'lumotlari
                </div>
                <div class="card-body">
                    <h4 class="text-primary">{{ $article->title }}</h4>
                    @if($article->abstract)
                        <p class="text-muted">{{ $article->abstract }}</p>
                    @endif
                    <hr>

                    <!-- Muallif ma'lumotlari -->
                    <div class="card border-warning mb-3">
                        <div class="card-header bg-warning bg-opacity-10">
                            <i class="bi bi-person-badge me-2"></i>Muallif ma'lumotlari
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong><i class="bi bi-person me-1"></i>Muallif ismi:</strong><br>
                                        <span class="fs-5">{{ $article->author_display_name }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    @if($article->author_affiliation)
                                        <p class="mb-2">
                                            <strong><i class="bi bi-building me-1"></i>Tashkilot:</strong><br>
                                            {{ $article->author_affiliation }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @if($article->author)
                                <div class="alert alert-info mb-0 mt-2">
                                    <small><i class="bi bi-link me-1"></i>Tizim foydalanuvchisi:
                                        <strong>{{ $article->author->name }}</strong> ({{ $article->author->email }})
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class="bi bi-calendar-event me-1"></i>Konferensiya:</strong>
                                {{ $article->conference->title }}</p>
                            <p><strong><i class="bi bi-globe me-1"></i>Davlat:</strong>
                                {{ $article->conference->country->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="bi bi-file-text me-1"></i>Sahifalar:</strong> {{ $article->page_range }}
                                ({{ $article->page_count }} bet)</p>
                            <p><strong><i class="bi bi-sort-numeric-up me-1"></i>Tartib raqami:</strong>
                                {{ $article->order_number }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- PDF yuklab olish tugmalari -->
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ Storage::url($article->pdf_path) }}" class="btn btn-primary" target="_blank">
                            <i class="bi bi-file-pdf me-1"></i>Asl PDF
                        </a>

                        @if($article->formatted_pdf_path)
                            <a href="{{ route('admin.articles.download-formatted', $article) }}" class="btn btn-success">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Formatlangan PDF
                            </a>
                        @else
                            <form action="{{ route('admin.articles.reformat', $article) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success">
                                    <i class="bi bi-magic me-1"></i>Formatlangan PDF yaratish
                                </button>
                            </form>
                        @endif

                        @if($article->formatted_pdf_path)
                            <form action="{{ route('admin.articles.reformat', $article) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Qayta formatlash
                                </button>
                            </form>
                        @endif

                        @if($article->article_link)
                            <a href="{{ $article->article_link }}" class="btn btn-outline-primary" target="_blank">
                                <i class="bi bi-link me-1"></i>Maqola havolasi
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle me-1"></i>Status
                </div>
                <div class="card-body text-center">
                    @if($article->status === 'published')
                        <span class="badge bg-success fs-5 px-4 py-2">
                            <i class="bi bi-check-circle me-1"></i>Nashr etilgan
                        </span>
                        <p class="text-muted mt-2 mb-0">{{ $article->published_at?->format('d.m.Y H:i') }}</p>
                    @else
                        <span class="badge bg-warning fs-5 px-4 py-2">
                            <i class="bi bi-clock me-1"></i>Kutilmoqda
                        </span>
                        <form action="{{ route('admin.articles.publish', $article) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-send me-1"></i>Nashr qilish
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Sertifikat -->
            <div class="card mb-4">
                <div class="card-header bg-warning bg-opacity-10">
                    <i class="bi bi-award me-1"></i>Sertifikat
                </div>
                <div class="card-body text-center">
                    @if($article->certificate)
                        <i class="bi bi-award text-warning" style="font-size:4rem;"></i>
                        <p class="mt-2 mb-1">
                            <strong class="text-primary">{{ $article->certificate->certificate_number }}</strong>
                        </p>
                        <p class="text-muted small mb-3">
                            Berilgan sana: {{ $article->certificate->issue_date->format('d.m.Y') }}
                        </p>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('admin.certificates.download', $article) }}" class="btn btn-success">
                                <i class="bi bi-download me-1"></i>Yuklab olish
                            </a>
                            <form action="{{ route('admin.certificates.regenerate', $article) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button class="btn btn-outline-warning" title="Qayta yaratish">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <i class="bi bi-question-circle text-muted" style="font-size:3rem;"></i>
                        <p class="text-muted mt-2">Sertifikat mavjud emas</p>
                        @if($article->status === 'published')
                            <form action="{{ route('admin.certificates.generate', $article) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="bi bi-award me-1"></i>Sertifikat yaratish
                                </button>
                            </form>
                        @else
                            <small class="text-muted">Avval maqolani nashr qiling</small>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Amallar -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-gear me-1"></i>Amallar
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Tahrirlash
                        </a>
                        <a href="{{ route('admin.conferences.show', $article->conference) }}"
                            class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Konferensiyaga qaytish
                        </a>
                        <form action="{{ route('admin.articles.destroy', $article) }}" method="POST"
                            onsubmit="return confirm('Haqiqatan ham o\'chirmoqchimisiz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-1"></i>O'chirish
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection