@extends('layouts.app')

@section('title', 'Archive - Konferensiya materiallari arxivlari')

@section('content')
    <!-- Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bi bi-house me-1"></i>Bosh
                            sahifa</a></li>
                    <li class="breadcrumb-item active">Archive</li>
                </ol>
            </nav>
        </div>
    </section>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="section-title d-inline-block">
                <i class="bi bi-archive me-2"></i>Konferensiyalar Arxivi
            </h1>
            <p class="text-muted mt-3">Yakunlangan konferensiya materiallari to'plamlari bilan tanishing</p>
        </div>

        <div class="row g-4">
            @forelse($conferences as $conference)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('conference.show', $conference) }}" class="text-decoration-none">
                        <div class="card conference-card h-100 border-0 shadow-sm overflow-hidden">
                            <!-- Cover Image Header -->
                            <div class="card-img-top position-relative" style="height: 180px; overflow: hidden;">
                                @if($conference->country->cover_image)
                                    <img src="{{ asset($conference->country->cover_image) }}" 
                                         alt="{{ $conference->title }}"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #1a3a5f 0%, #0d2137 100%); display: flex; align-items: center; justify-content: center;">
                                        @if($conference->country->flag_url)
                                            <img src="{{ Storage::url($conference->country->flag_url) }}" 
                                                 alt="{{ $conference->country->name }}"
                                                 style="width: 100px; height: 66px; object-fit: cover; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
                                        @else
                                            <div class="text-center">
                                                <i class="bi bi-journal-text text-white" style="font-size: 4rem; opacity: 0.5;"></i>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <!-- Date overlay -->
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-secondary shadow-sm px-3 py-2">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $conference->conference_date ? $conference->conference_date->format('d.m.Y') : \Carbon\Carbon::createFromFormat('Y-m', $conference->month_year)->translatedFormat('M Y') }}
                                    </span>
                                </div>
                                <!-- Country overlay -->
                                <div class="position-absolute bottom-0 start-0 m-3">
                                    <span class="badge bg-light text-dark px-3 py-2 shadow-sm">
                                        @if($conference->country->flag_url)
                                            <img src="{{ Storage::url($conference->country->flag_url) }}" 
                                                 style="width: 20px; height: 14px; object-fit: cover; border-radius: 3px; margin-right: 4px;">
                                        @endif
                                        {{ $conference->country->name }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Card Body -->
                            <div class="card-body p-4 d-flex flex-column">
                                <h6 class="card-title mb-2 flex-grow-1" style="color: var(--primary-blue); font-weight: 600; line-height: 1.4;">
                                    {{ Str::limit($conference->title, 100) }}
                                </h6>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="text-muted small">
                                        <i class="bi bi-collection me-1"></i>To'plam ochiq
                                    </span>
                                    <span class="btn btn-sm btn-outline-primary rounded-pill">
                                        Ko'rish <i class="bi bi-arrow-right ms-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-archive display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Hozircha arxivda konferensiyalar mavjud emas</h4>
                        <p class="text-muted">Yakunlangan konferensiyalar ushbu bo'limda paydo bo'ladi.</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        <div class="mt-4 d-flex justify-content-center">
            {{ $conferences->links() }}
        </div>
    </div>

    <style>
        .conference-card {
            transition: all 0.3s ease;
            border-radius: 12px;
        }
        .conference-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
        }
        .conference-card .card-img-top img {
            transition: transform 0.5s ease;
        }
        .conference-card:hover .card-img-top img {
            transform: scale(1.05);
        }
    </style>
@endsection
