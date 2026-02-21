@extends('layouts.admin')

@section('page-title', 'Yangi davlat')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="bi bi-globe me-2"></i>Yangi davlat qo'shish
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.countries.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomi (O'zbekcha)</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomi (Inglizcha)</label>
                        <input type="text" class="form-control" name="name_en" value="{{ old('name_en') }}" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Davlat kodi (3 ta harf)</label>
                        <input type="text" class="form-control" name="code" value="{{ old('code') }}" maxlength="3"
                            required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bayroq</label>
                        <input type="file" class="form-control" name="flag" accept="image/*">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                            <label class="form-check-label" for="is_active">Faol</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check me-1"></i>Saqlash
                    </button>
                    <a href="{{ route('admin.countries.index') }}" class="btn btn-secondary">Bekor qilish</a>
                </div>
            </form>
        </div>
    </div>
@endsection