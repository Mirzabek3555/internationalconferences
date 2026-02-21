@extends('layouts.admin')

@section('page-title', $country->name . ' - davlat ma\'lumotlari')

@section('content')
    <div class="row">
        <!-- Davlat ma'lumotlari -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-globe me-2"></i>{{ $country->name }} ({{ $country->name_en }})
                    </span>
                    <div>
                        <a href="{{ route('admin.countries.edit', $country) }}" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil me-1"></i>Tahrirlash
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Cover Image -->
                        <div class="col-md-5 mb-4">
                            @if($country->cover_image)
                                <img src="{{ asset($country->cover_image) }}" alt="{{ $country->name }}"
                                    class="img-fluid rounded shadow" style="max-height: 300px; object-fit: contain;">
                            @elseif($country->flag_url)
                                <div class="text-center p-4 bg-light rounded">
                                    <img src="{{ Storage::url($country->flag_url) }}" alt="{{ $country->name }}"
                                        style="max-height: 100px; border-radius: 5px; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">
                                </div>
                            @else
                                <div class="text-center p-5 bg-light rounded">
                                    <i class="bi bi-globe display-1 text-muted"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Ma'lumotlar -->
                        <div class="col-md-7">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 40%;"><i class="bi bi-hash me-1"></i>ID:</th>
                                    <td>{{ $country->id }}</td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-type me-1"></i>Nom (O'zbekcha):</th>
                                    <td>{{ $country->name }}</td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-translate me-1"></i>Nom (Inglizcha):</th>
                                    <td>{{ $country->name_en }}</td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-code me-1"></i>Kod:</th>
                                    <td><span class="badge bg-secondary">{{ $country->code }}</span></td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-toggle-on me-1"></i>Holat:</th>
                                    <td>
                                        @if($country->is_active)
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Faol</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Faol emas</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-calendar me-1"></i>Yaratilgan:</th>
                                    <td>{{ $country->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Konferensiya ma'lumotlari -->
            <div class="card mb-4">
                <div class="card-header bg-success bg-opacity-10">
                    <i class="bi bi-journal-bookmark me-2"></i>Konferensiya ma'lumotlari
                </div>
                <div class="card-body">
                    <h5 class="mb-3">
                        {{ $country->conference_name ?? 'Konferensiya nomi kiritilmagan' }}
                    </h5>
                    @if($country->conference_description)
                        <p class="text-muted mb-0">{{ $country->conference_description }}</p>
                    @else
                        <p class="text-muted mb-0 fst-italic">Tavsif kiritilmagan</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistika -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-info bg-opacity-10">
                    <i class="bi bi-bar-chart me-2"></i>Statistika
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-journal me-2"></i>Konferensiyalar:</span>
                        <span class="badge bg-primary fs-6">{{ $country->conferences->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span><i class="bi bi-file-text me-2"></i>Jami maqolalar:</span>
                        <span class="badge bg-success fs-6">{{ $country->conferences->sum('articles_count') }}</span>
                    </div>
                </div>
            </div>

            <!-- Konferensiyalar ro'yxati -->
            @if($country->conferences->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-list me-2"></i>Konferensiyalar
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($country->conferences as $conference)
                            <a href="{{ route('admin.conferences.show', $conference) }}"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $conference->month }}/{{ $conference->year }}</div>
                                    <small class="text-muted">{{ $conference->articles_count }} maqola</small>
                                </div>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Orqaga tugma -->
    <div class="mt-4">
        <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Davlatlar ro'yxatiga qaytish
        </a>
    </div>
@endsection