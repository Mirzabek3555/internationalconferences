@extends('layouts.admin')

@section('page-title', 'Yangi maqola qo\'shish')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Yangi maqola ma'lumotlari</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <!-- Asosiy ma'lumotlar -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Maqola sarlavhasi <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}"
                                required>
                        </div>

                        <!-- Fayl yuklash bo'limi -->
                        <div class="mb-4 p-3 border rounded"
                            style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                            <h6 class="mb-3"><i class="bi bi-cloud-upload me-2"></i>Hujjat yuklash</h6>

                            <!-- DOCX yuklash -->
                            <div class="mb-3 p-3 bg-primary bg-opacity-10 border border-primary rounded">
                                <label for="docx_file" class="form-label fw-bold text-primary">
                                    <i class="bi bi-file-earmark-word me-1"></i> DOCX fayl yuklash <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control @error('docx_file') is-invalid @enderror"
                                    id="docx_file" name="docx_file" accept=".docx,.doc" required>
                                @error('docx_file')
                                    <div class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                                <div class="form-text text-primary">
                                    <i class="bi bi-file-word me-1"></i><strong>Maqola matni (Word) faylini yuklang.</strong>
                                    Tizim undan matn va formulalarni o'qib PDF yaratadi.
                                    <br><small class="text-muted">Fayl hajmi: maksimum 20MB | Format: .docx, .doc</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="abstract" class="form-label">Annotatsiya (Abstract)</label>
                            <textarea class="form-control" id="abstract" name="abstract"
                                rows="4">{{ old('abstract') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="keywords" class="form-label">Kalit so'zlar (Keywords)</label>
                            <input type="text" class="form-control" id="keywords" name="keywords"
                                value="{{ old('keywords') }}"
                                placeholder="Masalan: matematik modellashtirish, algoritm, optimallashtirish">
                            <div class="form-text">Kalit so'zlarni vergul bilan ajrating</div>
                        </div>

                        <div class="mb-3">
                            <label for="references" class="form-label">Adabiyotlar (References)</label>
                            <textarea class="form-control" id="references" name="references" rows="5"
                                placeholder="1. Muallif F.I.Sh. Maqola nomi // Jurnal nomi. – 2024. – №1. – B. 1-10.">{{ old('references') }}</textarea>
                            <div class="form-text">Har bir manbani yangi qatordan yozing</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Qo'shimcha ma'lumotlar -->
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Muallif va Konferensiya</h6>

                                {{-- DAVLAT TANLASH --}}
                                <div class="mb-3">
                                    <label for="country_id" class="form-label">Davlat (Konferensiya) <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="country_id" name="country_id" required>
                                        <option value="">Tanlang...</option>
                                        @foreach($countries as $country)
                                            @php
                                                $preCountryId = $preselectedConference?->country_id;
                                                $isSelected = old('country_id')
                                                    ? old('country_id') == $country->id
                                                    : ($preCountryId == $country->id);
                                            @endphp
                                            <option value="{{ $country->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $country->name }} ({{ $country->name_en }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- OY KIRITISH - QOLDA (qo'lda kiritish, select emas) --}}
                                <div class="mb-3">
                                    <label for="month_year" class="form-label fw-bold">
                                        <i class="bi bi-calendar-month me-1"></i>
                                        Konferensiya oyi <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="month"
                                        class="form-control @error('month_year') is-invalid @enderror"
                                        id="month_year"
                                        name="month_year"
                                        value="{{ old('month_year', $preselectedConference?->month_year) }}"
                                        required
                                    >
                                    @error('month_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-info">
                                        <i class="bi bi-info-circle me-1"></i>
                                        O'tgan yoki kelgusi oyni ham kiritish mumkin. Maqola shu oy konferensiyasiga biriktiriladi.
                                    </div>
                                </div>

                                {{-- ANIQ SANA - QOLDA (12-mart, 20-aprel kabi) --}}
                                <div class="mb-3">
                                    <label for="conference_date" class="form-label fw-bold">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        Konferensiya aniq sanasi <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        class="form-control @error('conference_date') is-invalid @enderror"
                                        id="conference_date"
                                        name="conference_date"
                                        value="{{ old('conference_date', $preselectedConference?->conference_date?->format('Y-m-d')) }}"
                                        required
                                    >
                                    @error('conference_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-warning">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        Masalan: <strong>12-mart</strong> yoki <strong>20-aprel</strong> kabi aniq sana. PDF da "Date: 12th March 2026" ko'rinishida chiqadi.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="author_name" class="form-label">Asosiy muallif (F.I.Sh) <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="author_name" name="author_name"
                                        value="{{ old('author_name') }}" required placeholder="Masalan: Eshmatov Toshmat">
                                </div>

                                <div class="mb-3">
                                    <label for="author_affiliation" class="form-label">Ish joyi / O'qish joyi</label>
                                    <input type="text" class="form-control" id="author_affiliation"
                                        name="author_affiliation" value="{{ old('author_affiliation') }}"
                                        placeholder="Masalan: Toshkent Davlat Universiteti">
                                </div>

                                <div class="mb-3">
                                    <label for="co_authors" class="form-label">Qo'shimcha mualliflar (ixtiyoriy)</label>
                                    <textarea class="form-control" id="co_authors" name="co_authors" rows="3"
                                        placeholder="Har bir muallifni yangi qatordan yozing:&#10;Aliyev Ali, ToshDU&#10;Karimov Karim, SamDU">{{ old('co_authors') }}</textarea>
                                    <div class="form-text">Har bir muallifni yangi qatordan yozing. Format: Ism Familiya,
                                        Tashkilot</div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="publish_now" name="publish_now"
                                        value="1" {{ old('publish_now') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="publish_now">
                                        Saqlash va darhol nashr qilish
                                    </label>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i> Saqlash
                                    </button>
                                    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">
                                        Bekor qilish
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection