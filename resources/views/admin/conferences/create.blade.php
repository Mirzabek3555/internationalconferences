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
                        <select class="form-select" name="country_id" required>
                            <option value="">Tanlang...</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sana</label>
                        <input type="date" class="form-control" name="conference_date" value="{{ old('conference_date') }}"
                            required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sarlavha</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tavsif</label>
                    <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
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