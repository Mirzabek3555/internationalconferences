@extends('layouts.app')

@section('title', 'Konferensiyalar')

@section('content')
    <!-- Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bi bi-house me-1"></i>Bosh
                            sahifa</a></li>
                    <li class="breadcrumb-item active">Konferensiyalar</li>
                </ol>
            </nav>
        </div>
    </section>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="section-title d-inline-block">
                <i class="bi bi-journals me-2"></i>Barcha Konferensiyalar
            </h1>
            <p class="text-muted mt-3">Turli davlatlar bo'yicha xalqaro ilmiy konferensiyalar</p>
        </div>

        <div class="row g-4">
            @forelse($countries as $country)
                <div class="col-md-6">
                    <a href="{{ route('country.show', $country) }}" class="text-decoration-none">
                        <div class="card conference-card h-100 border-0 shadow-sm overflow-hidden">
                            <!-- Cover Image Header -->
                            <div class="card-img-top position-relative" style="height: 220px; overflow: hidden;">
                                @if($country->cover_image)
                                    <img src="{{ asset($country->cover_image) }}" 
                                         alt="{{ $country->name }}"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #1a3a5f 0%, #0d2137 100%); display: flex; align-items: center; justify-content: center;">
                                        @if($country->flag_url)
                                            <img src="{{ Storage::url($country->flag_url) }}" 
                                                 alt="{{ $country->name }}"
                                                 style="width: 120px; height: 80px; object-fit: cover; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
                                        @else
                                            <div class="text-center">
                                                <i class="bi bi-globe-americas text-white" style="font-size: 5rem; opacity: 0.5;"></i>
                                                <div class="text-white mt-2">{{ $country->code }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <!-- Country badge overlay -->
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-white text-dark shadow-sm px-3 py-2" style="font-size: 0.9rem;">
                                        @if($country->flag_url)
                                            <img src="{{ Storage::url($country->flag_url) }}" 
                                                 style="width: 24px; height: 16px; object-fit: cover; border-radius: 3px; margin-right: 6px;">
                                        @endif
                                        {{ $country->name }}
                                    </span>
                                </div>
                                <!-- Articles count -->
                                <div class="position-absolute bottom-0 start-0 m-3">
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="bi bi-file-earmark-text me-1"></i>{{ $country->articles_count ?? 0 }} maqola
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Card Body -->
                            <div class="card-body p-4">
                                <h5 class="card-title mb-2" style="color: var(--primary-blue); font-weight: 700; font-size: 1.2rem;">
                                    {{ $country->conference_name ?? 'Bu yerda konferensiya nomi yoziladi' }}
                                </h5>
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $country->name }} ({{ $country->name_en }})
                                </p>
                                @if($country->conference_description)
                                    <p class="card-text text-muted mb-3" style="font-size: 0.9rem;">
                                        {{ Str::limit($country->conference_description, 150) }}
                                    </p>
                                @else
                                    <p class="card-text text-muted mb-3" style="font-size: 0.9rem;">
                                        Ko'p sohali tadqiqotlar bo'yicha ilmiy konferensiya materiallari. Ushbu konferensiya materiallari konferensiya ishtirokchilari tomonidan taqdim etilgan original tadqiqot ishlarini nashr etadi.
                                    </p>
                                @endif
                                
                                <div class="d-flex justify-content-end">
                                    <span class="btn btn-primary">
                                        <i class="bi bi-arrow-right me-1"></i>Maqolalarni ko'rish
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-globe-americas display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">Hozircha konferensiyalar mavjud emas</h4>
                        <p class="text-muted">Tez orada yangi konferensiyalar qo'shiladi.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <style>
        .conference-card {
            transition: all 0.3s ease;
            border-radius: 15px;
        }
        .conference-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }
        .conference-card .card-img-top img {
            transition: transform 0.5s ease;
        }
        .conference-card:hover .card-img-top img {
            transform: scale(1.05);
        }
    </style>
@endsection