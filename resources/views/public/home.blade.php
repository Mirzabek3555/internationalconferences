@extends('layouts.app')

@section('title', 'Bosh sahifa')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container position-relative">
            <img src="{{ asset('images/logo.png') }}" alt="ISC - International Scientific Conferences" class="hero-logo">
            <h1 class="hero-title">International Scientific Conferences</h1>
            <p class="hero-subtitle">
                Xalqaro ilmiy konferensiyalar platformasi tadqiqotchilar va ta'lim mutaxassislariga o'z original ilmiy maqolalarini nashr etish imkoniyatini taqdim etadi. Bu ochiq kirish, ekspertlar tomonidan tekshiriladigan oylik konferensiya.
            </p>
            <p class="text-muted mb-4">
                <i class="bi bi-calendar3 me-2"></i>Nashr chastotasi: Oylik
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="#conferences" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-journal-text me-2"></i>Konferensiyalar
                </a>
                <a href="{{ route('countries') }}" class="btn btn-outline-primary btn-lg px-4">
                    <i class="bi bi-globe me-2"></i>Barcha davlatlar
                </a>
            </div>
        </div>
        <div class="hero-wave"></div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">{{ $countries->count() }}+</div>
                        <div class="stat-label"><i class="bi bi-globe me-1"></i>Davlatlar</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">{{ $totalArticles ?? '500' }}+</div>
                        <div class="stat-label"><i class="bi bi-file-text me-1"></i>Maqolalar</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">{{ $countries->count() }}+</div>
                        <div class="stat-label"><i class="bi bi-calendar-event me-1"></i>Konferensiyalar</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Conferences Section - 2 per row -->
    <section class="py-5" id="conferences">
        <div class="container">
            <h2 class="section-title text-center">
                <i class="bi bi-journals me-2"></i>Konferensiyalar
            </h2>
            <p class="text-center text-muted mb-5">
                Ko'p sohali tadqiqotlar bo'yicha ilmiy konferensiya materiallari elektron konferensiya seriyalari hisoblanadi.
            </p>

            <div class="row g-4">
                @forelse($countries as $country)
                    <div class="col-md-6">
                        <a href="{{ route('country.show', $country) }}" class="text-decoration-none">
                            <div class="card conference-card h-100 border-0 shadow-sm overflow-hidden">
                                <!-- Cover Image Header -->
                                <div class="card-img-top position-relative" style="height: 280px; overflow: hidden; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                    @if($country->cover_image)
                                        <img src="{{ asset($country->cover_image) }}" 
                                             alt="{{ $country->name }}"
                                             style="width: 100%; height: 100%; object-fit: contain; padding: 10px;">
                                    @else
                                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #1a3a5f 0%, #0d2137 100%); display: flex; align-items: center; justify-content: center;">
                                            @if($country->flag_url)
                                                <img src="{{ Storage::url($country->flag_url) }}" 
                                                     alt="{{ $country->name }}"
                                                     style="width: 100px; height: 65px; object-fit: cover; border-radius: 5px; box-shadow: 0 5px 20px rgba(0,0,0,0.3);">
                                            @else
                                                <i class="bi bi-globe-americas text-white" style="font-size: 4rem; opacity: 0.5;"></i>
                                            @endif
                                        </div>
                                    @endif
                                    <!-- Country badge overlay -->
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge bg-white text-dark shadow-sm px-3 py-2">
                                            @if($country->flag_url)
                                                <img src="{{ Storage::url($country->flag_url) }}" 
                                                     style="width: 20px; height: 14px; object-fit: cover; border-radius: 2px; margin-right: 5px;">
                                            @endif
                                            {{ $country->name }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Card Body -->
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-2" style="color: var(--primary-blue); font-weight: 700;">
                                        {{ $country->conference_name ?? 'Bu yerda konferensiya nomi yoziladi' }}
                                    </h5>
                                    <p class="text-muted small mb-3">
                                        {{ $country->name }} ({{ $country->name_en }})
                                    </p>
                                    @if($country->conference_description)
                                        <p class="card-text small text-muted mb-3">
                                            {{ Str::limit($country->conference_description, 120) }}
                                        </p>
                                    @else
                                        <p class="card-text small text-muted mb-3">
                                            Ko'p sohali tadqiqotlar bo'yicha ilmiy konferensiya materiallari. Ushbu konferensiya materiallari konferensiya ishtirokchilari tomonidan taqdim etilgan original tadqiqot ishlarini nashr etadi.
                                        </p>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-primary me-2">
                                                <i class="bi bi-file-earmark-text me-1"></i>{{ $country->articles_count ?? 0 }} maqola
                                            </span>
                                        </div>
                                        <span class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-arrow-right me-1"></i>Maqolalarni ko'rish
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-globe display-1 text-muted"></i>
                        <p class="text-muted mt-3">Hozircha konferensiyalar mavjud emas.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="section-title">Biz haqimizda</h2>
                    <p class="text-muted mb-4">
                        International Scientific Conferences (ISC) tadqiqotchilar va ta'lim mutaxassislariga o'z original ilmiy maqolalarini
                        ta'lim amaliyoti va tegishli sohalarda nashr etish imkoniyatini taqdim etadi.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Ochiq kirish</strong> - Barcha maqolalar bepul o'qish uchun mavjud
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Ekspert tekshiruvi</strong> - Barcha maqolalar ekspertlar tomonidan tekshiriladi
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Oylik nashr</strong> - Har oy yangi konferensiyalar
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Sertifikat</strong> - Mualliflar uchun sertifikatlar
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4">
                                <i class="bi bi-info-circle me-2 text-primary"></i>Qanday ishlaydi?
                            </h5>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-circle p-2">1</span>
                                </div>
                                <div class="ms-3">
                                    <strong>Ro'yxatdan o'ting</strong>
                                    <p class="text-muted small mb-0">Platformada hisob yarating</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-circle p-2">2</span>
                                </div>
                                <div class="ms-3">
                                    <strong>Maqola yuklang</strong>
                                    <p class="text-muted small mb-0">O'z ilmiy maqolangizni yuklang</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary rounded-circle p-2">3</span>
                                </div>
                                <div class="ms-3">
                                    <strong>Tekshiruv</strong>
                                    <p class="text-muted small mb-0">Ekspertlar maqolani tekshiradi</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <span class="badge bg-success rounded-circle p-2">4</span>
                                </div>
                                <div class="ms-3">
                                    <strong>Nashr va sertifikat</strong>
                                    <p class="text-muted small mb-0">Maqola nashr etiladi va sertifikat beriladi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white" style="background: var(--gradient-header);">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">O'z maqolangizni joylashtirmoqchimisiz?</h2>
            <p class="opacity-75 mb-4">
                Bizning platformamizda o'z ilmiy maqolangizni joylashtiring va sertifikat oling.
                Maqolalarni o'qish uchun ro'yxatdan o'tish shart emas!
            </p>
            @guest
                <a href="{{ route('login') }}" class="btn btn-light btn-lg px-5">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Kirish
                </a>
            @else
                <a href="{{ route('user.dashboard') }}" class="btn btn-light btn-lg px-5">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboardga o'tish
                </a>
            @endguest
        </div>
    </section>

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