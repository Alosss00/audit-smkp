@if($kriteria->relasiReferensi->isNotEmpty())
    @php
        $jumlahRef = $kriteria->relasiReferensi->count();

        // Bangun konten HTML untuk Bootstrap Popover
        $popoverContent = '<div style="max-height: 280px; overflow-y: auto; font-size: 0.78rem;">';
        $popoverContent .= '<div class="fw-semibold text-muted mb-2" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">';
        $popoverContent .= $jumlahRef . ' Elemen Terkait</div>';

        foreach ($kriteria->relasiReferensi as $ref) {
            $desc = $ref->deskripsi_keterkaitan
                ? e(mb_substr($ref->deskripsi_keterkaitan, 0, 90)) . (mb_strlen($ref->deskripsi_keterkaitan) > 90 ? '...' : '')
                : 'Lihat konteks keterkaitan di Matriks SMKP.';

            $popoverContent .= '<div class="d-flex align-items-start gap-2 mb-2 pb-2 border-bottom">';
            $popoverContent .= '<span class="badge bg-secondary font-monospace flex-shrink-0" style="font-size:0.68rem; padding: 2px 5px;">' . e($ref->kriteria_tujuan_kode) . '</span>';
            $popoverContent .= '<span class="text-muted" style="line-height: 1.4;">' . $desc . '</span>';
            $popoverContent .= '</div>';
        }
        $popoverContent .= '</div>';
    @endphp

    <button type="button"
            class="btn btn-info btn-relasi-referensi text-white p-0 border-0 ms-1 mt-1 d-block shadow-sm"
            data-bs-toggle="popover"
            data-bs-trigger="click"
            data-bs-placement="right"
            data-bs-html="true"
            data-bs-title='<span style="font-size:0.8rem;"><i class="bi bi-link-45deg text-info me-1"></i> Elemen Referensi &bull; <span class="badge bg-info text-dark">{{ $jumlahRef }}</span></span>'
            data-bs-content="{{ $popoverContent }}"
            data-bs-custom-class="relasi-referensi-popover"
            title="Lihat {{ $jumlahRef }} elemen referensi terkait"
            aria-label="Referensi terkait {{ $kriteria->kode_kriteria }}"
            style="border-radius: 12px; font-size: 0.7rem; padding: 2px 8px !important; font-weight: 600; min-width: 32px; height: 22px; display: inline-flex; align-items: center; justify-content: center;">
            <i class="bi bi-link-45deg me-1" style="font-size: 0.8rem;"></i> {{ $jumlahRef }}
    </button>
@endif
