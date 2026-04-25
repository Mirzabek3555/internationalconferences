@extends('layouts.admin')

@section('page-title', 'Konferensiyani tahrirlash')

@section('content')
    <div class="card">
        <div class="card-header"><i class="bi bi-pencil me-2"></i>{{ Str::limit($conference->title, 40) }} - tahrirlash
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form action="{{ route('admin.conferences.update', $conference) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Davlat</label>
                        <select class="form-select" name="country_id" required>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ $conference->country_id == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Boshlanish Sanasi (ixtiyoriy)</label>
                        <input type="date" class="form-control" name="start_date"
                            value="{{ optional($conference->start_date)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tugash Sanasi (ixtiyoriy)</label>
                        <input type="date" class="form-control" name="end_date"
                            value="{{ optional($conference->end_date)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Asosiy Sana</label>
                        <input type="date" class="form-control" name="conference_date"
                            value="{{ $conference->conference_date->format('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sarlavha</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title', $conference->title) }}"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tavsif</label>
                    <textarea class="form-control" name="description"
                        rows="3">{{ old('description', $conference->description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option value="draft" {{ $conference->status == 'draft' ? 'selected' : '' }}>Qoralama</option>
                        <option value="active" {{ $conference->status == 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="completed" {{ $conference->status == 'completed' ? 'selected' : '' }}>Yakunlangan
                        </option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check me-1"></i>Yangilash</button>
                    <a href="{{ route('admin.conferences.index') }}" class="btn btn-secondary">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>
@endsection