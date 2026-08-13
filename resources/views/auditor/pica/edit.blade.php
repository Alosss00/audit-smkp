@extends('layouts.app')

@section('title', 'Tindak Lanjut PICA — Auditee / PIC Area')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9 col-lg-8">
        <div class="mb-3">
            <a href="{{ route('auditor.pica.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar PICA Area
            </a>
        </div>

        <div class="card card-custom p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-slate-800 mb-0">Form Respon & Tindak Lanjut PICA</h4>
                        <p class="text-muted small mb-0">
                            Area: <strong>{{ $pica->auditDetail->auditSesi->area_audit ?? '-' }}</strong> | 
                            Kriteria: <strong>{{ $pica->auditDetail->kriteria->kode_kriteria ?? '-' }}</strong>
                        </p>
                    </div>
                </div>
                <div>
                    @if($pica->status === 'open')
                        <span class="badge bg-danger badge-role fs-6">Status: OPEN</span>
                    @elseif($pica->status === 'in_progress')
                        <span class="badge bg-warning text-dark badge-role fs-6">Status: IN PROGRESS</span>
                    @else
                        <span class="badge bg-success badge-role fs-6">Status: CLOSED</span>
                    @endif
                </div>
            </div>

            <!-- Finding Description Box -->
            <div class="p-3 bg-light rounded-3 border mb-4">
                <h6 class="fw-bold text-slate-800 mb-1"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Deskripsi Temuan Audit (Assessor / Lead Auditor):</h6>
                <p class="text-slate-800 mb-0 small">{{ $pica->deskripsi_temuan }}</p>
            </div>

            <!-- Audit Trail / Last Log Info -->
            @if($lastLog)
                <div class="alert alert-info rounded-3 p-2 px-3 mb-4 small">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    Terakhir diubah oleh <strong>{{ $lastLog->user->name ?? 'Sistem' }}</strong> pada <strong>{{ $lastLog->waktu_perubahan->format('d M Y H:i') }}</strong>
                </div>
            @endif

            <form action="{{ route('auditor.pica.update', $pica->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Section Editable oleh Responden / Auditee -->
                <div class="mb-3">
                    <label for="akar_masalah" class="form-label fw-bold small text-slate-800">
                        Akar Masalah <span class="text-danger">*</span>
                    </label>
                    <textarea name="akar_masalah" id="akar_masalah" rows="3" class="form-control" placeholder="Jelaskan analisis penyebab utama ketidaksesuaian ini..." required>{{ old('akar_masalah', $pica->akar_masalah) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="tindakan_koreksi" class="form-label fw-bold small text-slate-800">Tindakan Perbaikan (Koreksi Immediate)</label>
                    <textarea name="tindakan_koreksi" id="tindakan_koreksi" rows="3" class="form-control" placeholder="Tindakan langsung yang telah/akan dilakukan untuk mengatasi temuan...">{{ old('tindakan_koreksi', $pica->tindakan_koreksi) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="tindakan_pencegahan" class="form-label fw-bold small text-slate-800">Tindakan Pencegahan (Preventive Action)</label>
                    <textarea name="tindakan_pencegahan" id="tindakan_pencegahan" rows="3" class="form-control" placeholder="Langkah-langkah jangka panjang agar ketidaksesuaian tidak terulang...">{{ old('tindakan_pencegahan', $pica->tindakan_pencegahan) }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="bukti_perbaikan" class="form-label fw-bold small text-slate-800">Upload Bukti Perbaikan (Foto / Dokumen PDF / Zip)</label>
                    <input type="file" name="bukti_perbaikan" id="bukti_perbaikan" class="form-control" accept="image/*,.pdf,.doc,.docx,.zip">
                    @if($pica->bukti_perbaikan)
                        <div class="mt-2">
                            <a href="{{ $pica->bukti_perbaikan_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-paperclip me-1"></i> Lihat Bukti Terunggah
                            </a>
                        </div>
                    @endif
                </div>

                <hr class="my-4">

                <!-- Section Read-Only / Disabled (Hanya Bisa Diubah Oleh Lead Auditor / Admin) -->
                <div class="p-3 bg-slate-50 border rounded-3 mb-4" style="background-color: #f8fafc;">
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-lock-fill me-1"></i>Otoritas Lead Auditor / Admin (Read-Only)</h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Tenggat Waktu (Deadline)</label>
                            <input type="text" class="form-control bg-light text-muted" value="{{ $pica->tenggat_waktu ? $pica->tenggat_waktu->format('d M Y') : 'Belum Ditentukan' }}" disabled>
                            <small class="text-muted" style="font-size: 0.75rem;">Ditentukan oleh Lead Auditor</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-secondary">Status PICA</label>
                            <input type="text" class="form-control bg-light text-muted text-uppercase fw-bold" value="{{ $pica->status }}" disabled>
                            <small class="text-muted" style="font-size: 0.75rem;">Status diubah oleh Lead Auditor saat verifikasi</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-secondary">Catatan Verifikasi Auditor</label>
                            <textarea class="form-control bg-light text-muted" rows="2" disabled>{{ $pica->catatan_verifikasi_auditor ?? 'Belum ada catatan verifikasi' }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                    <a href="{{ route('auditor.pica.index') }}" class="btn btn-outline-secondary rounded-3 px-4">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Respon PICA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
