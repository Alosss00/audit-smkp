<?php
/**
 * Panel Navigasi 7 Langkah Perangkat Audit SMKP per Sesi Audit.
 * Variabel yang diharapkan: $sesi (App\Models\AuditSesi), $activeStep (string opsional).
 */
$perangkatSteps = $sesi->getPerangkatAuditSteps();
$activeStep = $activeStep ?? '';
$isAuditorNav = auth()->check() && auth()->user()->role === 'auditor';

$stepUrlMap = $isAuditorNav ? [
    'tahap_awal' => ['url' => route('auditor.audit-sesi.cetak-tahap-awal', $sesi->id), 'target' => '_blank'],
    'rencana'    => ['url' => route('auditor.audit-sesi.rencana.show', $sesi->id), 'target' => '_self'],
    'matrix'     => ['url' => route('auditor.audit-sesi.rekap', $sesi->id), 'target' => '_self'],
    'kesesuaian' => ['url' => route('auditor.audit-sesi.cetak-kesesuaian', $sesi->id), 'target' => '_blank'],
    'pica'       => ['url' => route('auditor.pica.index'), 'target' => '_self'],
    'ofi'        => ['url' => route('auditor.audit-sesi.ofi.index', $sesi->id), 'target' => '_self'],
    'rekap'      => ['url' => route('auditor.audit-sesi.rekap', $sesi->id), 'target' => '_self'],
] : [
    'tahap_awal' => ['url' => route('admin.audit-sesi.tahap-awal', $sesi->id), 'target' => '_self'],
    'rencana'    => ['url' => route('admin.audit-sesi.rencana.edit', $sesi->id), 'target' => '_self'],
    'matrix'     => ['url' => route('admin.audit-sesi.matrix', $sesi->id), 'target' => '_self'],
    'kesesuaian' => ['url' => route('admin.audit-sesi.cetak-kesesuaian', $sesi->id), 'target' => '_blank'],
    'pica'       => ['url' => route('admin.pica.index', ['perusahaan_id' => $sesi->perusahaan_id, 'tahun_periode' => $sesi->tahun_periode]), 'target' => '_self'],
    'ofi'        => ['url' => route('admin.audit-sesi.ofi.index', $sesi->id), 'target' => '_self'],
    'rekap'      => ['url' => route('admin.audit-sesi.rekap', $sesi->id), 'target' => '_self'],
];
?>
<div class="card card-custom p-3 mb-4 bg-white border-start border-4 border-primary">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <div class="small fw-bold text-slate-800">
            <i class="bi bi-compass-fill text-primary me-1"></i>Perangkat Audit SMKP (7 Langkah)
        </div>
        <span class="small text-muted">Klik langkah untuk berpindah antar formulir sesi ini</span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($perangkatSteps as $st): ?>
            <?php
                $isActive = $activeStep === $st['key'];
                $targetMeta = $stepUrlMap[$st['key']] ?? ['url' => '#', 'target' => '_self'];
                $badgeCls = match ($st['status']) {
                    'final' => 'bg-success text-white',
                    'draft' => 'bg-warning text-dark',
                    default => 'bg-secondary bg-opacity-25 text-secondary',
                };
                $statusLabel = match ($st['status']) {
                    'final' => 'Final',
                    'draft' => 'Draft',
                    default => 'Belum Diisi',
                };
            ?>
            <a href="<?php echo e($targetMeta['url']); ?>"
               target="<?php echo e($targetMeta['target']); ?>"
               class="btn btn-sm <?php echo $isActive ? 'btn-primary text-white shadow-sm' : 'btn-light border text-slate-800'; ?> rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 text-decoration-none">
                <i class="bi <?php echo e($st['icon']); ?>"></i>
                <span class="fw-semibold"><?php echo e($st['no']); ?>. <?php echo e($st['label']); ?></span>
                <?php if ($st['count'] !== null): ?>
                    <span class="badge bg-dark bg-opacity-10 <?php echo $isActive ? 'text-white' : 'text-dark'; ?> rounded-pill"><?php echo e($st['count']); ?></span>
                <?php endif; ?>
                <span class="badge rounded-pill <?php echo e($badgeCls); ?>" style="font-size: 0.68rem;"><?php echo e($statusLabel); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>
