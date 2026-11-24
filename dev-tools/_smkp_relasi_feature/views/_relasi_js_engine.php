<?php
/*
    =====================================================================
    PARTIAL: _relasi_js_engine.php
    =====================================================================
    JavaScript Engine untuk kedua komponen relasi UI.

    CARA PENGGUNAAN di matrix.php:
    Tempatkan include ini tepat sebelum tag penutup </body> atau
    di dalam scripts pada layout, SETELAH semua script lain.

        echo view('partials._relasi_js_engine')->render();

    FUNGSI YANG DICAKUP:
    1. initRelasiKunciBanner()     — Trigger banner peringatan saat nilai berubah
    2. initRelasiReferensiPopover() — Inisialisasi Bootstrap Popover untuk referensi
    3. Event delegasi untuk dismissal banner
    =====================================================================
*/
?>
<style nonce="<?php echo e($cspNonce ?? ''); ?>">
/* -- Popover Relasi Referensi — Override Bootstrap -- */
.relasi-referensi-popover {
    --bs-popover-max-width: 340px;
    --bs-popover-border-color: rgba(6, 182, 212, 0.3);
    --bs-popover-box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}
.relasi-referensi-popover .popover-header {
    background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%);
    border-bottom: 1px solid rgba(6, 182, 212, 0.2);
    font-size: 0.8rem;
    padding: 8px 12px;
}
.relasi-referensi-popover .popover-body {
    padding: 10px 12px;
}

/* -- Banner Animasi Slide-Down -- */
.relasi-kunci-banner-row {
    animation: slideDownBanner 0.25s ease-out;
}
@keyframes slideDownBanner {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* -- Ikon Referensi hover effect -- */
.btn-relasi-referensi:hover .badge {
    background-color: rgba(6, 182, 212, 0.25) !important;
}
</style>

<script nonce="<?php echo e($cspNonce ?? ''); ?>">
(function () {
    'use strict';

    /**
     * Ambang pemicu peringatan KUNCI.
     * Banner muncul saat: nilai > 0 DAN nilai < nilai_maksimal
     * (artinya ada temuan / tidak sepenuhnya comply).
     */
    const BANNER_TRIGGER_CONDITION = (nilaiSaat, nilaiMaks) =>
        nilaiSaat > 0 && nilaiSaat < nilaiMaks;

    // -- 1. Inisialisasi Banner Kunci ------------------------------------------

    function initRelasiKunciBanner() {
        const nilaInputs = document.querySelectorAll('.nilai-input');

        nilaInputs.forEach(function (input) {
            const kriteriaId = input.dataset.kriteriaId;
            if (!kriteriaId) return;

            const bannerRow = document.querySelector(
                '.relasi-kunci-banner-row[data-for-kriteria="' + kriteriaId + '"]'
            );
            if (!bannerRow) return; // Tidak ada relasi kunci untuk kriteria ini

            const nilaiMaks = parseFloat(
                input.closest('tr')?.dataset?.nilaiMaksimal ?? input.getAttribute('max') ?? 4
            ) || 4;

            function evaluateBanner() {
                const nilaiSaat = parseFloat(input.value) || 0;
                const isNaChecked = document.querySelector(
                    '.na-checkbox[data-kriteria-id="' + kriteriaId + '"]'
                )?.checked ?? false;

                // Sembunyikan banner jika N/A atau nilai tidak memenuhi kondisi trigger
                if (isNaChecked || !BANNER_TRIGGER_CONDITION(nilaiSaat, nilaiMaks)) {
                    bannerRow.style.display = 'none';
                    return;
                }

                // Tampilkan banner
                bannerRow.style.display = '';
            }

            // Event listeners
            input.addEventListener('input',  evaluateBanner);
            input.addEventListener('change', evaluateBanner);

            // Quick score buttons
            const scoreButtons = document.querySelectorAll(
                '.score-btn-group .btn-score-quick[data-kriteria-id="' + kriteriaId + '"],' +
                'tr[data-kriteria-id="' + kriteriaId + '"] .btn-score-quick'
            );
            // Fallback: cari di dalam row yang sama
            const kriteriaRow = input.closest('tr.kriteria-row');
            if (kriteriaRow) {
                kriteriaRow.querySelectorAll('.btn-score-quick').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        // Nilai sudah diupdate oleh handler lain, tunggu microtask
                        setTimeout(evaluateBanner, 50);
                    });
                });
            }

            // N/A checkbox
            const naCheckbox = document.querySelector(
                '.na-checkbox[data-kriteria-id="' + kriteriaId + '"]'
            );
            if (naCheckbox) {
                naCheckbox.addEventListener('change', evaluateBanner);
            }

            // Evaluasi initial saat halaman dimuat
            evaluateBanner();
        });
    }

    // -- 2. Dismissal Banner (tombol X) ---------------------------------------

    document.addEventListener('click', function (e) {
        const dismissBtn = e.target.closest('.btn-dismiss-relasi-banner');
        if (!dismissBtn) return;

        const kriteriaId = dismissBtn.dataset.kriteriaId;
        const bannerRow = document.querySelector(
            '.relasi-kunci-banner-row[data-for-kriteria="' + kriteriaId + '"]'
        );
        if (bannerRow) {
            bannerRow.style.display = 'none';
        }
    });

    // -- 3. Inisialisasi Bootstrap Popover Referensi ---------------------------

    function initRelasiReferensiPopover() {
        const popoverTriggers = document.querySelectorAll('[data-bs-toggle="popover"].btn-relasi-referensi');

        popoverTriggers.forEach(function (el) {
            // Inisialisasi Bootstrap Popover
            const popover = new bootstrap.Popover(el, {
                sanitize: false, // Izinkan HTML di content (sudah di-escape)
            });

            // Auto-tutup popover lain saat satu dibuka (satu sekaligus)
            el.addEventListener('shown.bs.popover', function () {
                document.querySelectorAll('[data-bs-toggle="popover"].btn-relasi-referensi').forEach(
                    function (other) {
                        if (other !== el) {
                            bootstrap.Popover.getInstance(other)?.hide();
                        }
                    }
                );
            });
        });

        // Tutup semua popover saat klik di luar
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.btn-relasi-referensi') && !e.target.closest('.popover')) {
                document.querySelectorAll('[data-bs-toggle="popover"].btn-relasi-referensi').forEach(
                    function (el) { bootstrap.Popover.getInstance(el)?.hide(); }
                );
            }
        });
    }

    // -- Bootstrap -------------------------------------------------------------

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initRelasiKunciBanner();
            initRelasiReferensiPopover();
        });
    } else {
        initRelasiKunciBanner();
        initRelasiReferensiPopover();
    }

})();
</script>
