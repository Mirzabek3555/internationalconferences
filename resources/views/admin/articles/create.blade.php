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

                            <!-- PDF yuklash - Formulalar saqlanadi -->
                            <div class="mb-3 p-3 bg-success bg-opacity-10 border border-success rounded">
                                <label for="pdf_file" class="form-label fw-bold text-success">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> PDF fayl yuklash (Tavsiya etiladi)
                                </label>
                                <input type="file" class="form-control @error('pdf_file') is-invalid @enderror"
                                    id="pdf_file" name="pdf_file" accept=".pdf">
                                @error('pdf_file')
                                    <div class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                                <div class="form-text text-success">
                                    <i class="bi bi-check-circle me-1"></i><strong>Eng yaxshi variant!</strong>
                                    Matematik formulalar 100% saqlanadi. PDF kontenti o'zgartirilmaydi, faqat dizayn
                                    qo'shiladi.
                                    <br><small class="text-muted">Fayl hajmi: maksimum 20MB | Format: .pdf</small>
                                </div>
                            </div>

                            <div class="text-center my-2">
                                <span class="badge bg-secondary">YOKI</span>
                            </div>

                            <!-- Matn kiritish -->
                            <div class="p-3 bg-light border rounded">
                                <label for="content" class="form-label fw-bold">
                                    <i class="bi bi-textarea-t me-1"></i> Maqola matni (Text formatda)
                                </label>
                                <textarea class="form-control" id="content" name="content" rows="12"
                                    placeholder="Maqola matnini shu yerga joylashtiring (agar fayl yuklamasangiz)...">{{ old('content') }}</textarea>
                                <div class="form-text">
                                    <small class="text-muted">Agar fayl yuklanmasa, matnni shu yerga kiriting. Formulalar
                                        oddiy matn sifatida ko'rsatiladi.</small>
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

                                <div class="mb-3">
                                    <label for="country_id" class="form-label">Davlat (Konferensiya) <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="country_id" name="country_id" required>
                                        <option value="">Tanlang...</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ (old('country_id') == $country->id || request('conference_id') && \App\Models\Conference::find(request('conference_id'))->country_id == $country->id) ? 'selected' : '' }}>
                                                {{ $country->name }} ({{ $country->name_en }})
                                            </option>
                                        @endforeach
                                    </select>
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
                                <h6 class="card-title">Texnik ma'lumotlar</h6>

                                <div class="mb-3">
                                    <label for="start_page" class="form-label">Boshlanish sahifasi <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="start_page" name="start_page"
                                        value="{{ old('start_page', 1) }}" min="1" required>
                                </div>

                                <div class="mb-3">
                                    <label for="order_number" class="form-label">Tartib raqami <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="order_number" name="order_number"
                                        value="{{ old('order_number', 1) }}" min="1" required>
                                </div>

                                <hr>

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