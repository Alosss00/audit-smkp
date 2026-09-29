{{--
    =====================================================================
    PARTIAL: _relasi_referensi_icon.blade.php
    =====================================================================
    Komponen Panel Referensi Pasif (Untuk relasi "referensi")

    CARA PENGGUNAAN di matrix.blade.php:
    Di dalam <td> yang menampilkan kode kriteria (kolom pertama tabel),
    tambahkan @include ini tepat setelah badge kode kriteria:

        <td class="fw-bold align-top pt-3">
            <span class="badge bg-dark font-monospace fs-6 py-1 px-2">
                {{ $kriteria->kode_kriteria }}
            </span>
            {{-- TAMBAHKAN BARIS INI: --}}
            @include('partials._relasi_referensi_icon', ['kriteria' => $kriteria])
        </td>

    PERILAKU:
    - Hanya tampil jika kriteria memiliki relasi referensi (>0).
    - Berupa ikon kecil yang memunculkan Bootstrap Popover saat diklik.
    - Popover berisi daftar kode dan deskripsi singkat elemen terkait.
    - TIDAK ada trigger otomatis — murni interaksi manual pengguna.
    =====================================================================
--}}

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
            class="btn btn-sm btn-relasi-referensi p-0 border-0 ms-1 mt-1 d-block"
            data-bs-toggle="popover"
            data-bs-trigger="click"
            data-bs-placement="right"
            data-bs-html="true"
            data-bs-title='<span style="font-size:0.8rem;"><i class="bi bi-link-45deg text-info me-1"></i> Elemen Referensi &bull; <span class="badge bg-info text-dark">{{ $jumlahRef }}</span></span>'
            data-bs-content="{{ $popoverContent }}"
            data-bs-custom-class="relasi-referensi-popover"
            title="Lihat {{ $jumlahRef }} elemen referensi terkait"
            aria-label="Referensi terkait {{ $kriteria->kode_kriteria }}"
            style="background: none; line-height: 1;">
        <span class="badge rounded-pill bg-info bg-opacity-15 text-info border border-info border-opacity-25"
              style="font-size: 0.65rem; padding: 2px 5px; cursor: pointer; letter-spacing: 0.02em;">
            <i class="bi bi-link-45deg" style="font-size: 0.7rem;"></i>
            {{ $jumlahRef }}
        </span>
    </button>
@endif
