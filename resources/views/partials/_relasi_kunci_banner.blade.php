@if($kriteria->relasiKunci->isNotEmpty())
<tr class="relasi-kunci-banner-row"
    data-for-kriteria="{{ $kriteria->id }}"
    style="display: none;">
    <td colspan="6" class="p-0 border-0">
        <div class="relasi-kunci-banner mx-3 mb-2 px-3 py-2 rounded-3 border-start border-4 border-warning"
             style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
                    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15);">

            {{-- Header Banner --}}
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size: 1.1rem;">&#128161;</span>
                    <span class="fw-bold text-warning-emphasis" style="font-size: 0.8rem; letter-spacing: 0.02em;">
                        SARAN PENELUSURAN — Periksa Konsistensi Elemen Terkait
                    </span>
                </div>
                <button type="button"
                        class="btn-close btn-close-sm btn-dismiss-relasi-banner"
                        aria-label="Tutup saran"
                        data-kriteria-id="{{ $kriteria->id }}"
                        title="Tutup saran ini (bisa muncul kembali saat nilai diubah)"
                        style="width: 0.7rem; height: 0.7rem; opacity: 0.5;"></button>
            </div>

            {{-- Daftar Relasi Kunci --}}
            <div class="d-flex flex-column gap-1">
                @foreach($kriteria->relasiKunci as $relasi)
                    <div class="d-flex align-items-start gap-2 py-1 px-2 rounded-2"
                         style="background: rgba(255,255,255,0.6); font-size: 0.78rem;">
                        <span class="badge bg-warning text-dark font-monospace flex-shrink-0 mt-0"
                              style="font-size: 0.72rem; padding: 2px 6px;">
                            {{ $relasi->kriteria_tujuan_kode }}
                        </span>
                        <span class="text-amber-900" style="color: #78350f; line-height: 1.45;">
                            @if($relasi->deskripsi_keterkaitan)
                                {{ $relasi->deskripsi_keterkaitan }}
                            @else
                                Periksa konsistensi temuan pada kriteria ini.
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </td>
</tr>
@endif
