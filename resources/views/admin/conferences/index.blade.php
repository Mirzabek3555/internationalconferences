@extends('layouts.admin')

@section('page-title', 'Konferensiyalar')

@section('content')
    <!-- Davlatlar va konferensiya nomlari -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-globe me-2"></i>Davlatlar va konferensiya nomlari
            <small class="ms-2 opacity-75">(saytda ko'rinadigan nomlar)</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Bayroq</th>
                        <th style="width: 180px;">Davlat</th>
                        <th>Konferensiya nomi (saytda ko'rinadi)</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 100px;">Amallar</th>
                        <th style="width: 80px;">Cover</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($countries as $country)
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
                                <small class="text-muted">{{ $country->name_en }}</small>
                            </td>
                            <td>
                                <!-- Inline edit for conference name -->
                                <div class="conference-name-wrapper" data-country-id="{{ $country->id }}">
                                    <div class="display-mode d-flex align-items-center">
                                        @if($country->conference_name && $country->conference_name !== 'Bu yerda konferensiya nomi yoziladi')
                                            <span class="text-success fw-bold conference-text">
                                                <i class="bi bi-check-circle me-1"></i>{{ $country->conference_name }}
                                            </span>
                                        @else
                                            <span class="text-warning conference-text">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Bu yerda konferensiya nomi yoziladi
                                            </span>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-3 edit-btn">
                                            <i class="bi bi-pencil me-1"></i>
                                        </button>
                                    </div>
                                    <div class="edit-mode d-none">
                                        <div class="input-group">
                                            <input type="text" class="form-control conference-input"
                                                value="{{ ($country->conference_name && $country->conference_name !== 'Bu yerda konferensiya nomi yoziladi') ? $country->conference_name : '' }}"
                                                placeholder="Masalan: International Scientific Conference on Modern Technologies">
                                            <button type="button" class="btn btn-success save-btn" title="Saqlash">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary cancel-btn"
                                                title="Bekor qilish">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($country->is_active)
                                    <span class="badge bg-success">Faol</span>
                                @else
                                    <span class="badge bg-danger">Nofaol</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.countries.edit', $country) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square me-1"></i>Tahrirlash
                                </a>
                            </td>
                            <td>
                                @if($country->cover_image)
                                    <img src="{{ asset($country->cover_image) }}"
                                        style="width:50px;height:35px;object-fit:cover;border-radius:4px;">
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Konferensiyalar ro'yxati -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar-event me-2"></i>Barcha konferensiyalar</span>
            <a href="{{ route('admin.conferences.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus me-1"></i>Yangi konferensiya
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Davlat</th>
                        <th>Sarlavha</th>
                        <th>Sana</th>
                        <th>Maqolalar</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($conferences as $conference)
                        <tr>
                            <td>
                                @if($conference->country->flag_url)
                                    <img src="{{ Storage::url($conference->country->flag_url) }}"
                                        style="width:25px;height:16px;object-fit:cover;border-radius:2px;" class="me-1">
                                @endif
                                {{ $conference->country->name }}
                            </td>
                            <td style="max-width: 300px;">
                                <div class="text-truncate" title="{{ $conference->title }}">
                                    <strong>{{ $conference->title }}</strong>
                                </div>
                            </td>
                            <td>{{ $conference->conference_date->format('d.m.Y') }}</td>
                            <td><span class="badge bg-primary">{{ $conference->articles_count }}</span></td>
                            <td>
                                @if($conference->status === 'active')
                                    <span class="badge bg-success">Faol</span>
                                @elseif($conference->status === 'completed')
                                    <span class="badge bg-secondary">Yakunlangan</span>
                                @else
                                    <span class="badge bg-warning">Qoralama</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.conferences.show', $conference) }}"
                                    class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.conferences.edit', $conference) }}"
                                    class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.conferences.destroy', $conference) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Konferensiyalar mavjud emas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($conferences->hasPages())
            <div class="card-footer">{{ $conferences->links() }}</div>
        @endif
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
                    wrapper.querySelector('.conference-input').select();
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
                    const saveBtn = this;

                    // Disable button while saving
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Saqlanmoqda...';

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
                                textSpan.innerHTML = '<i class="bi bi-check-circle me-1"></i>' + newName;
                                textSpan.className = 'text-success fw-bold conference-text';
                            } else {
                                textSpan.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Bu yerda konferensiya nomi yoziladi';
                                textSpan.className = 'text-warning conference-text';
                            }

                            // Switch back to display mode
                            wrapper.querySelector('.display-mode').classList.remove('d-none');
                            wrapper.querySelector('.edit-mode').classList.add('d-none');

                            // Show success message
                            showToast('Muvaffaqiyatli saqlandi!', 'success');
                        } else {
                            showToast('Xatolik yuz berdi: ' + (data.message || 'Nomalum xatolik'), 'danger');
                        }
                    } catch (error) {
                        showToast('Xatolik yuz berdi: ' + error.message, 'danger');
                    } finally {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Saqlash';
                    }
                });
            });

            // Enter key to save, Escape to cancel
            document.querySelectorAll('.conference-input').forEach(input => {
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        this.closest('.edit-mode').querySelector('.save-btn').click();
                    } else if (e.key === 'Escape') {
                        this.closest('.edit-mode').querySelector('.cancel-btn').click();
                    }
                });
            });

            // Toast notification function
            function showToast(message, type = 'success') {
                const toastContainer = document.getElementById('toast-container') || createToastContainer();
                const toast = document.createElement('div');
                toast.className = `toast align-items-center text-white bg-${type} border-0 show`;
                toast.setAttribute('role', 'alert');
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                toastContainer.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }

            function createToastContainer() {
                const container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                container.style.zIndex = '1100';
                document.body.appendChild(container);
                return container;
            }
        });
    </script>
@endpush