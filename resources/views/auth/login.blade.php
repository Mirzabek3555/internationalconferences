@extends('layouts.app')

@section('title', 'Kirish')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <!-- Card Header with Logo -->
                    <div class="text-center py-4" style="background: var(--light-blue);">
                        <img src="{{ asset('images/logo.png') }}" alt="ISC" style="max-height: 80px;">
                    </div>

                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold" style="color: var(--primary-dark);">Kirish</h3>
                            <p class="text-muted">Hisobingizga kiring</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger border-0" style="border-radius: 10px;">
                                @foreach($errors->all() as $error)
                                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"
                                        style="background: var(--light-blue); border-color: #e2e8f0;">
                                        <i class="bi bi-envelope" style="color: var(--primary-blue);"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}" required autofocus style="border-color: #e2e8f0;"
                                        placeholder="email@example.com">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium">Parol</label>
                                <div class="input-group">
                                    <span class="input-group-text"
                                        style="background: var(--light-blue); border-color: #e2e8f0;">
                                        <i class="bi bi-lock" style="color: var(--primary-blue);"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password" required
                                        style="border-color: #e2e8f0;" placeholder="••••••••">
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember"
                                    style="border-color: var(--primary-blue);">
                                <label class="form-check-label" for="remember">Meni eslab qol</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Kirish
                            </button>
                        </form>

                        <div class="text-center mt-4 pt-3" style="border-top: 1px solid #e2e8f0;">
                            <p class="text-muted mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Maqolalarni o'qish uchun ro'yxatdan o'tish shart emas!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection