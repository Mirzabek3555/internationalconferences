@extends('layouts.admin')

@section('page-title', 'Davlatlar')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <span><i class="bi bi-globe me-2"></i>Barcha davlatlar ({{ $countries->count() }})</span>
            <a href="{{ route('admin.countries.create') }}" class="btn btn-light btn-sm">
                <i class="bi bi-plus me-1"></i>Yangi davlat
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Bayroq</th>
                        <th style="width: 200px;">Davlat</th>
                        <th>Konferensiya nomi</th>
                        <th style="width: 80px;">Cover</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 150px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countries as $country)
                        <tr>
                            <td>
                                @if($country->flag_url)
                                    <img src="{{ Storage::url($country->flag_url) }}"
                                        style="width:45px;height:30px;object-fit:cover;border-radius:4px;box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                @else
                                    <span class="badge bg-secondary">{{ $country->code }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $country->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $country->name_en }} ({{ $country->code }})</small>
                            </td>
                            <td>
                                <!-- Inline edit for conference name -->
                                <div class="conference-name-wrapper" data-country-id="{{ $country->id }}">
                                    <div class="display-mode">
                                        @if($country->conference_name && $country->conference_name !== 'Bu yerda konferensiya nomi yoziladi')
                                            <span
                                                class="text-primary fw-bold conference-text">{{ $country->conference_name }}</span>
                                        @else
                                            <span class="text-muted fst-italic conference-text">Nom berilmagan</span>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-link text-primary p-0 ms-2 edit-btn"
                                            title="Tahrirlash">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                    <div class="edit-mode d-none">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control conference-input"
                                                value="{{ $country->conference_name !== 'Bu yerda konferensiya nomi yoziladi' ? $country->conference_name : '' }}"
                                                placeholder="Konferensiya nomini kiriting...">
                                            <button type="button" class="btn btn-success save-btn" title="Saqlash">
                                                <i class="bi bi-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-secondary cancel-btn" title="Bekor qilish">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($country->cover_image)
                                    <img src="{{ asset($country->cover_image) }}"
                                        style="width:50px;height:35px;object-fit:cover;border-radius:4px;box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i></span>
                                @endif
                            </td>
                            <td>
                                @if($country->is_active)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Faol</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Nofaol</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.countries.show', $country) }}" class="btn btn-sm btn-outline-info"
                                        title="Ko'rish">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.countries.edit', $country) }}"
                                        class="btn btn-sm btn-outline-primary" title="Tahrirlash">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.articles.index', ['country' => $country->id]) }}"
                                        class="btn btn-sm btn-outline-success" title="Maqolalar">
                                        <i class="bi bi-file-text"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-globe display-4 d-block mb-3"></i>
                                Davlatlar mavjud emas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-4">
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Eslatma:</strong> Konferensiya nomini o'zgartirish uchun qalam ikonasini bosing yoki to'liq tahrirlash
            uchun "tahrirlash" tugmasini bosing.
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Edit button click
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const wrapper = this.closest('.conference-name-wrapper');
                    wrapper.querySelector('.display-mode').classList.add('d-none');
                    wrapper.querySelector('.edit-mode').classList.remove('d-none');
                    wrapper.querySelector('.conference-input').focus();
                });
            });

            // Cancel button click
            document.querySelectorAll('.cancel-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const wrapper = this.closest('.conference-name-wrapper');
                    wrapper.querySelector('.display-mode').classList.remove('d-none');
                    wrapper.querySelector('.edit-mode').classList.add('d-none');
                });
            });

            // Save button click
            document.querySelectorAll('.save-btn').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const wrapper = this.closest('.conference-name-wrapper');
                    const countryId = wrapper.dataset.countryId;
                    const input = wrapper.querySelector('.conference-input');
                    const newName = input.value.trim();

                    try {
                        const response = await fetch(`/admin/countries/${countryId}/update-conference-name`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ conference_name: newName })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Update display text
                            const textSpan = wrapper.querySelector('.conference-text');
                            if (newName) {
                                textSpan.textContent = newName;
                                textSpan.className = 'text-primary fw-bold conference-text';
                            } else {
                                textSpan.textContent = 'Nom berilmagan';
                                textSpan.className = 'text-muted fst-italic conference-text';
                            }

                            // Switch back to display mode
                            wrapper.querySelector('.display-mode').classList.remove('d-none');
                            wrapper.querySelector('.edit-mode').classList.add('d-none');
                        } else {
                            alert('Xatolik yuz berdi: ' + (data.message || 'Nomalum xatolik'));
                        }
                    } catch (error) {
                        alert('Xatolik yuz berdi: ' + error.message);
                    }
                });
            });

            // Enter key to save
            document.querySelectorAll('.conference-input').forEach(input => {
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        this.closest('.edit-mode').querySelector('.save-btn').click();
                    } else if (e.key === 'Escape') {
                        this.closest('.edit-mode').querySelector('.cancel-btn').click();
                    }
                });
            });
        });
    </script>
@endpush