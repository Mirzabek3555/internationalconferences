@extends('layouts.admin')

@section('page-title', $conference->title)

@section('content')
    <div class="row">
        <div class="col-md-4">
            <!-- Konferensiya ma'lumotlari -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-calendar-event me-2"></i>Konferensiya ma'lumotlari
                </div>
                <div class="card-body">
                    @if($conference->country->flag_url)
                        <div class="text-center mb-3">
                            <img src="{{ Storage::url($conference->country->flag_url) }}"
                                style="width:80px;height:55px;object-fit:cover;border-radius:8px;box-shadow: 0 3px 10px rgba(0,0,0,0.2);">
                        </div>
                    @endif
                    <h5 class="text-primary text-center">{{ $conference->title }}</h5>
                    <p class="text-center text-muted mb-3">{{ $conference->country->name }}
                        ({{ $conference->country->name_en }})</p>

                    <div class="row text-center">
                        <div class="col-6">
                            <p class="mb-1 small text-muted">Sana</p>
                            <p class="mb-0 fw-bold">{{ $conference->conference_date->format('d.m.Y') }}</p>
                        </div>
                        <div class="col-6">
                            <p class="mb-1 small text-muted">Maqolalar</p>
                            <p class="mb-0 fw-bold">{{ $conference->articles->count() }} ta</p>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center">
                        @if($conference->status === 'active')
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i>Faol
                            </span>
                        @elseif($conference->status === 'completed')
                            <span class="badge bg-secondary fs-6 px-3 py-2">
                                <i class="bi bi-flag me-1"></i>Yakunlangan
                            </span>
                        @else
                            <span class="badge bg-warning fs-6 px-3 py-2">
                                <i class="bi bi-pencil me-1"></i>Qoralama
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- To'plam yaratish -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-collection me-2"></i>Maqolalar to'plami
                </div>
                <div class="card-body">
                    <!-- Oddiy to'plam -->
                    <form action="{{ route('admin.conferences.collection', $conference) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-success w-100">
                            <i class="bi bi-file-pdf me-1"></i>Oddiy to'plam (mundarija)
                        </button>
                    </form>

                    <!-- To'liq to'plam -->
                    <form action="{{ route('admin.conferences.full-collection', $conference) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-book me-1"></i>To'liq to'plam yaratish
                            <small class="d-block">Barcha maqolalar birlashtiriladi</small>
                        </button>
                    </form>

                    <!-- Yuklab olish -->
                    @if($conference->pdf_collection_path || $conference->collection_pdf_path)
                        <a href="{{ route('admin.conferences.download-collection', $conference) }}"
                            class="btn btn-warning w-100 mb-2">
                            <i class="bi bi-download me-1"></i>To'plamni yuklab olish
                        </a>
                    @endif

                    <!-- Yakunlash -->
                    @if($conference->status !== 'completed')
                        <hr>
                        <form action="{{ route('admin.conferences.complete', $conference) }}" method="POST"
                            onsubmit="return confirm('Konferensiyani yakunlamoqchimisiz? To\'plam yaratiladi.')">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-flag-fill me-1"></i>Konferensiyani yakunlash
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success mb-0 text-center">
                            <i class="bi bi-check-circle me-1"></i>
                            Bu konferensiya yakunlangan
                        </div>
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
                        <a href="{{ route('admin.conferences.edit', $conference) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Tahrirlash
                        </a>
                        <a href="{{ route('admin.conferences.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Orqaga
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Maqolalar ro'yxati -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-text me-2"></i>Maqolalar</span>
                    <a href="{{ route('admin.articles.create') }}?conference_id={{ $conference->id }}"
                        class="btn btn-primary btn-sm">
                        <i class="bi bi-plus me-1"></i>Maqola qo'shish
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">#</th>
                                <th>Sarlavha</th>
                                <th>Muallif</th>
                                <th width="80">Sahifa</th>
                                <th width="100">Status</th>
                                <th width="130">Sertifikat</th>
                                <th width="80"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($conference->articles as $article)
                                <tr>
                                    <td class="text-muted">{{ $article->order_number }}</td>
                                    <td>
                                        <a href="{{ route('admin.articles.show', $article) }}" class="text-decoration-none">
                                            {{ Str::limit($article->title, 40) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ Str::limit($article->author_display_name, 25) }}</span>
                                        @if($article->author_affiliation)
                                            <br><small class="text-muted">{{ Str::limit($article->author_affiliation, 30) }}</small>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $article->page_range }}</td>
                                    <td>
                                        @if($article->status === 'published')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check me-1"></i>Nashr
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock me-1"></i>Kutish
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($article->certificate)
                                            <a href="{{ route('admin.certificates.download', $article) }}"
                                                class="badge bg-info text-decoration-none">
                                                <i
                                                    class="bi bi-award me-1"></i>{{ Str::limit($article->certificate->certificate_number, 15) }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-outline-info"
                                                title="Ko'rish">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($article->status === 'pending')
                                                <form action="{{ route('admin.articles.publish', $article) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-success" title="Nashr qilish">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                        <p class="mt-2 mb-0">Maqolalar hali qo'shilmagan</p>
                                        <a href="{{ route('admin.articles.create') }}?conference_id={{ $conference->id }}"
                                            class="btn btn-primary mt-3">
                                            <i class="bi bi-plus me-1"></i>Birinchi maqolani qo'shish
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($conference->articles->count() > 0)
                    <div class="card-footer bg-light">
                        <div class="row text-center">
                            <div class="col">
                                <strong class="text-primary">{{ $conference->articles->count() }}</strong>
                                <span class="text-muted">jami maqola</span>
                            </div>
                            <div class="col">
                                <strong
                                    class="text-success">{{ $conference->articles->where('status', 'published')->count() }}</strong>
                                <span class="text-muted">nashr etilgan</span>
                            </div>
                            <div class="col">
                                <strong
                                    class="text-warning">{{ $conference->articles->where('status', 'pending')->count() }}</strong>
                                <span class="text-muted">kutilmoqda</span>
                            </div>
                            <div class="col">
                                <strong
                                    class="text-info">{{ $conference->articles->whereNotNull('certificate')->count() }}</strong>
                                <span class="text-muted">sertifikat</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection