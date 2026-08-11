@extends('layouts.app')

@section('title', 'Login — Audit Internal SMKP Minerba')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        min-height: 100vh;
    }
    .login-container {
        max-width: 440px;
        margin: auto;
    }
    .login-card {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
    }
    .btn-login {
        background: linear-gradient(135deg, #0284c7, #0369a1);
        border: none;
        font-weight: 700;
        letter-spacing: 0.3px;
        transition: all 0.3s ease;
    }
    .btn-login:hover {
        background: linear-gradient(135deg, #0369a1, #075985);
        transform: translateY(-1px);
        box-shadow: 0 10px 20px -5px rgba(2, 132, 199, 0.4);
    }
    .demo-badge {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .demo-badge:hover {
        transform: scale(1.03);
    }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100 py-4">
    <div class="login-container w-100">
        
        <!-- Header / Logo -->
        <div class="text-center text-white mb-4">
            <div class="d-inline-flex align-items-center justify-content-center bg-info bg-opacity-25 rounded-circle p-3 mb-3 border border-info border-opacity-25">
                <i class="bi bi-shield-check text-info display-5"></i>
            </div>
            <h3 class="fw-bold tracking-tight mb-1">SMKP MINERBA</h3>
            <p class="text-light opacity-75 small">Sistem Informasi Audit Internal — Kepdirjen 185</p>
        </div>

        <!-- Form Card -->
        <div class="card login-card p-4 p-md-5">
            <h4 class="fw-bold text-slate-800 mb-1 text-center">Masuk ke Akun</h4>
            <p class="text-muted text-center small mb-4">Silakan masukkan username dan password Anda</p>

            @if($errors->any())
                <div class="alert alert-danger rounded-3 py-2 px-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div class="small fw-semibold">
                            {{ $errors->first() }}
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Username Field -->
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold small text-secondary">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" name="username" id="username" 
                            class="form-control bg-light border-start-0 @error('username') is-invalid @enderror" 
                            placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" id="password" 
                            class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" 
                            placeholder="Masukkan password" required>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small text-secondary" for="remember">
                            Ingat Saya
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-login w-100 py-3 rounded-3 text-white mb-4">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Sekarang
                </button>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="p-3 bg-light rounded-3 border">
                <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                    <i class="bi bi-key-fill text-warning me-1"></i> Akun Pengujian (Klik untuk Autofill):
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-danger demo-badge p-2 flex-grow-1 text-center" onclick="fillCredentials('admin', 'password')">
                        <i class="bi bi-shield-lock me-1"></i> Admin: <code>admin</code>
                    </span>
                    <span class="badge bg-info text-dark demo-badge p-2 flex-grow-1 text-center" onclick="fillCredentials('auditor', 'password')">
                        <i class="bi bi-clipboard-check me-1"></i> Auditor: <code>auditor</code>
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fillCredentials(username, password) {
        document.getElementById('username').value = username;
        document.getElementById('password').value = password;
    }
</script>
@endpush
