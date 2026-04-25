@extends('layouts.admin')

@section('page-title', 'Konferensiya to\'plamlari')

@section('content')

{{-- FILTER PANELI --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-funnel me-2"></i>Filtrlash</span>

    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.articles.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-muted small mb-1">Davlat bo'yicha</label>
                <select name="country_id" class="form-select">
                    <option value="">— Barcha davlatlar —</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small mb-1">Oy bo'yicha</label>
                <select name="month_year" class="form-select">
                    <option value="">— Barcha oylar —</option>
                    @php
                        $monthNames = [
                            '01'=>'Yanvar','02'=>'Fevral','03'=>'Mart',
                            '04'=>'Aprel','05'=>'May','06'=>'Iyun',
                            '07'=>'Iyul','08'=>'Avgust','09'=>'Sentabr',
                            '10'=>'Oktabr','11'=>'Noyabr','12'=>'Dekabr',
                        ];
                    @endphp
                    @foreach($availableMonths as $my)
                        @php [$yr, $mo] = explode('-', $my); @endphp
                        <option value="{{ $my }}" {{ request('month_year') === $my ? 'selected' : '' }}>
                            {{ $monthNames[$mo] ?? $mo }} {{ $yr }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search me-1"></i>Filtrlash
                </button>
                @if(request()->hasAny(['country_id','month_year']))
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

@if($conferences->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-inbox display-4 d-block mb-3"></i>
            <h5>Hech qanday konferensiya to'plami topilmadi</h5>
            <p class="mb-0">Yangi maqola qo'shsangiz, avtomatik to'plam yaratiladi.</p>
        </div>
    </div>
@else
    {{-- OYLAR BO'YICHA TO'PLAMLAR (KATEGORIYALANGAN) --}}
    <div class="accordion" id="conferencesAccordion">
        @php
            $groupedConferences = $conferences->groupBy('month_year');
        @endphp

        @foreach($groupedConferences as $monthYear => $monthConferences)
            @php
                [$confYear, $confMonth] = explode('-', $monthYear);
                $monthLabel = ($monthNames[$confMonth] ?? $confMonth) . ' ' . $confYear;
            @endphp
            
            <div class="accordion-item mb-3 border-0 shadow-sm rounded">
                <h2 class="accordion-header" id="heading-{{ $monthYear }}">
                    <button class="accordion-button collapsed fw-bold fs-5 bg-white border border-bottom-0 rounded-top" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $monthYear }}" aria-expanded="false" aria-controls="collapse-{{ $monthYear }}">
                        <i class="bi bi-calendar-check me-2 text-primary"></i> {{ $monthLabel }} ({{ $monthConferences->count() }} ta to'plam)
                    </button>
                </h2>
                <div id="collapse-{{ $monthYear }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $monthYear }}" data-bs-parent="#conferencesAccordion">
                    <div class="accordion-body bg-light border border-top-0 rounded-bottom p-3">
                        @foreach($monthConferences as $conference)
                            @php
                                $totalArticles = $conference->articles->count();
                                $publishedCount = $conference->articles->where('status', 'published')->count();
                                $pendingCount   = $totalArticles - $publishedCount;
                            @endphp

        <div class="card mb-4 shadow-sm" id="conf-{{ $conference->id }}">
            {{-- TO'PLAM SARLAVHASI --}}
            <div class="card-header"
                style="background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%); color: #fff;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <span class="badge bg-light text-dark me-2 fs-6">
                            <i class="bi bi-calendar3 me-1"></i>{{ $monthLabel }}
                        </span>
                        <strong class="fs-6">
                            {{ $conference->country->name }}
                            @if($conference->country->conference_name)
                                — {{ $conference->country->conference_name }}
                            @endif
                        </strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-white text-dark">
                            <i class="bi bi-files me-1"></i>{{ $totalArticles }} maqola
                        </span>
                        @if($publishedCount > 0)
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>{{ $publishedCount }} nashr
                            </span>
                        @endif
                        @if($pendingCount > 0)
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-hourglass me-1"></i>{{ $pendingCount }} kutmoqda
                            </span>
                        @endif
                        {{-- Status --}}
                        @if($conference->status === 'completed')
                            <span class="badge bg-secondary">Yakunlangan</span>
                        @elseif($conference->status === 'active')
                            <span class="badge bg-success">Faol</span>
                        @else
                            <span class="badge bg-light text-dark">Loyiha</span>
                        @endif
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.conferences.edit', $conference) }}" 
                               class="btn btn-light text-primary" title="To'plamni tahrirlash">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($conference->status !== 'completed')
                                <form action="{{ route('admin.conferences.complete', $conference) }}" method="POST" class="d-inline" onsubmit="return confirm('Ushbu konferensiyani yakunlashni tasdiqlaysizmi? Yakunlangach asosiy sahifadagi Arxiv bo\'limiga o\'tadi.')">
                                    @csrf
                                    <button type="submit" class="btn btn-light text-success" title="Yakunlash">
                                        <i class="bi bi-flag-fill"></i>
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('admin.conferences.destroy', $conference) }}" 
                                  method="POST" class="d-inline" 
                                  onsubmit="return confirm('DIQQAT! Ushbu to\'plamni o\'chirish undagi BARCHA maqolalarning o\'chib ketishiga olib kelishi mumkin. Tasdiqlaysizmi?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-light text-danger" title="To'plamni o'chirish">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <a href="{{ route('admin.articles.create', ['conference_id' => $conference->id]) }}"
                               class="btn btn-light text-success" title="Maqola qo'shish">
                                <i class="bi bi-plus-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MAQOLALAR JADVALI --}}
            <div class="card-body p-0">
                @if($conference->articles->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-folder-x me-2"></i>Bu oy uchun maqolalar mavjud emas
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width:40px">#</th>
                                    <th>Maqola sarlavhasi</th>
                                    <th>Muallif(lar)</th>
                                    <th>Yuklangan sana</th>
                                    <th>Betlar</th>
                                    <th>Fayl</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($conference->articles->sortBy('order_number') as $article)
                                    <tr>
                                        <td class="ps-3 text-muted small">{{ $article->order_number }}</td>
                                        <td>
                                            <strong>{{ Str::limit($article->title, 55) }}</strong>
                                        </td>
                                        <td class="small">
                                            <span>{{ $article->author_name }}</span>
                                            @if($article->co_authors)
                                                <br><span class="text-muted">+ {{ Str::limit($article->co_authors, 30) }}</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted text-nowrap">
                                            {{ $article->created_at->format('d.m.Y') }}
                                        </td>
                                        <td class="small">{{ $article->page_range }}</td>
                                        <td>
                                            @if($article->pdf_path)
                                                <a href="{{ route('admin.articles.download-formatted', $article) }}"
                                                   class="btn btn-sm btn-outline-danger" title="PDF yuklab olish" target="_blank">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($article->status === 'published')
                                                <span class="badge bg-success">Nashr</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Kutmoqda</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.articles.show', $article) }}"
                                                   class="btn btn-outline-info" title="Ko'rish">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.articles.edit', $article) }}"
                                                   class="btn btn-outline-primary" title="Tahrirlash">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @if($article->status === 'pending')
                                                    <form action="{{ route('admin.articles.publish', $article) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-success" title="Nashr qilish">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('admin.articles.destroy', $article) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Maqolani o\'chirishni tasdiqlaysizmi?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" title="O'chirish">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- TO'PLAM FOOTER --}}
            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2 py-2">
                <small class="text-muted">
                    <i class="bi bi-calendar-event me-1"></i>
                    {{ $conference->conference_date ? $conference->conference_date->format('d.m.Y') : $monthLabel }}
                </small>
                <div class="d-flex gap-2">
                    @if($conference->collection_pdf_path)
                        <a href="{{ route('admin.conferences.download-collection', $conference) }}"
                           class="btn btn-sm btn-outline-success">
                            <i class="bi bi-download me-1"></i>To'plamni yuklab olish
                        </a>
                    @endif
                    <a href="{{ route('admin.conferences.show', $conference) }}"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-folder me-1"></i>To'plam tafsilotlari
                    </a>
                </div>
            </div>
        </div>
        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection