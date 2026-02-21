@extends('layouts.admin')

@section('page-title', 'Davlatni tahrirlash')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-pencil me-2"></i>{{ $country->name }} - tahrirlash
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.countries.update', $country) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <!-- Davlat ma'lumotlari -->
                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary bg-opacity-10">
                        <i class="bi bi-globe me-2"></i>Davlat ma'lumotlari
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-bold">Nomi (O'zbekcha) <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="name"
                                    value="{{ old('name', $country->name) }}" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-bold">Nomi (Inglizcha) <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="name_en"
                                    value="{{ old('name_en', $country->name_en) }}" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label fw-bold">Kod <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg text-center text-uppercase"
                                    name="code" value="{{ old('code', $country->code) }}" maxlength="3" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Konferensiya ma'lumotlari -->
                <div class="card mb-4 border-success">
                    <div class="card-header bg-success bg-opacity-10">
                        <i class="bi bi-journal-bookmark me-2"></i>Konferensiya ma'lumotlari
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-bookmark me-1"></i>Konferensiya nomi
                            </label>
                            <input type="text" class="form-control form-control-lg" name="conference_name"
                                value="{{ old('conference_name', $country->conference_name) }}"
                                placeholder="Masalan: International Scientific Conference on Modern Technologies">
                            <small class="text-muted">Saytda davlat kartochkasida ko'rsatiladi</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-text-paragraph me-1"></i>Konferensiya tavsifi
                            </label>
                            <textarea class="form-control" name="conference_description" rows="3"
                                placeholder="Konferensiya haqida qisqacha ma'lumot...">{{ old('conference_description', $country->conference_description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Rasmlar -->
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <i class="bi bi-image me-2"></i>Rasmlar
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-flag me-1"></i>Bayroq
                                </label>
                                @if($country->flag_url)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($country->flag_url) }}"
                                            style="height:50px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                        <small class="text-muted ms-2">Joriy bayroq</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="flag" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-card-image me-1"></i>Cover rasm (sarlavha)
                                </label>
                                @if($country->cover_image)
                                    <div class="mb-2">
                                        <img src="{{ asset($country->cover_image) }}"
                                            style="height:60px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                        <small class="text-muted ms-2">Joriy cover</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control" name="cover_image" accept="image/*">
                                <small class="text-muted">Davlat ramzlari bilan bezatilgan sarlavha rasmi</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                style="width: 3em; height: 1.5em;" {{ $country->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold ms-2" for="is_active">
                                <i class="bi bi-check-circle me-1"></i>Faol
                            </label>
                        </div>
                        <small class="text-muted">Faol davlatlar saytda ko'rsatiladi</small>
                    </div>
                </div>

                <!-- Amallar -->
                <div class="d-flex gap-2 justify-content-between">
                    <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-1"></i>Bekor qilish
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-1"></i>Yangilash
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection