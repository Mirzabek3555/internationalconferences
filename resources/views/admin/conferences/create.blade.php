@extends('layouts.admin')

@section('page-title', 'Yangi konferensiya')

@section('content')
    <div class="card">
        <div class="card-header"><i class="bi bi-calendar-event me-2"></i>Yangi konferensiya yaratish</div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form action="{{ route('admin.conferences.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Davlat</label>
                        <select class="form-select" name="country_id" id="countrySelect" required>
                            <option value="">Tanlang...</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" 
                                    data-conf-name="{{ $country->conference_name }}" 
                                    data-conf-desc="{{ $country->conference_description }}"
                                    {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Boshlanish Sanasi (ixtiyoriy)</label>
                        <input type="date" class="form-control" name="start_date" value="{{ old('start_date') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tugash Sanasi (ixtiyoriy)</label>
                        <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Asosiy Sana</label>
                        <input type="date" class="form-control" name="conference_date" value="{{ old('conference_date') }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sarlavha</label>
                    <input type="text" class="form-control" name="title" id="titleInput" value="{{ old('title') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tavsif</label>
                    <textarea class="form-control" name="description" id="descInput" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Qoralama</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Yakunlangan</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Saqlash</button>
                    <a href="{{ route('admin.conferences.index') }}" class="btn btn-secondary">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countrySelect = document.getElementById('countrySelect');
        const titleInput = document.getElementById('titleInput');
        const descInput = document.getElementById('descInput');

        countrySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const confName = selectedOption.getAttribute('data-conf-name');
                const confDesc = selectedOption.getAttribute('data-conf-desc');
                
                if (confName) titleInput.value = confName;
                if (confDesc) descInput.value = confDesc;
            }
        });
    });
</script>
@endpush