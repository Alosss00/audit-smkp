@extends('layouts.app')

@section('title', 'Buat Sesi Audit Baru — SMKP Minerba')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="mb-3">
            <a href="{{ route('auditor.audit-sesi.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Sesi Audit
            </a>
        </div>

        <div class="card card-custom p-4 p-md-5">
            <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
                <div class="stat-icon-box bg-info bg-opacity-10 text-info">
                    <i class="bi bi-journal-plus"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-slate-800 mb-0">Buat Sesi Audit Baru</h4>
                    <p class="text-muted small mb-0">Inisialisasi form matriks penilaian SMKP Kepdirjen 185</p>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <ul class="mb-0 small ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('auditor.audit-sesi.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="tanggal_audit" class="form-label fw-semibold small text-secondary">Tanggal Pelaksanaan Audit</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                        <input type="date" name="tanggal_audit" id="tanggal_audit" class="form-control" value="{{ old('tanggal_audit', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="area_audit" class="form-label fw-semibold small text-secondary">Area / Lokasi Audit</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" name="area_audit" id="area_audit" class="form-control" placeholder="Contoh: Pit A / Processing Plant / Bengkel Utama" value="{{ old('area_audit') }}" required>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('auditor.audit-sesi.index') }}" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                        <i class="bi bi-arrow-right-circle me-1"></i> Lanjut Isi Matriks
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
