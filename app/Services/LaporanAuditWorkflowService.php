<?php

namespace App\Services;

use App\Models\AuditLaporan;
use App\Models\AuditLaporanDistribusi;
use App\Models\AuditLaporanSnapshot;
use App\Models\AuditLog;
use App\Models\MasterDasarHukum;
use App\Models\MasterTemplateLaporan;
use App\Models\User;
use App\Support\Terbilang;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LaporanAuditWorkflowService
{
    public function __construct(protected LaporanTemplateRenderer $renderer)
    {
    }

    /**
     * Format kode temuan seragam untuk seluruh kategori (NC-KRT-001, NC-MYR-001, NC-MNR-001).
     */
    public static function formatKodeTemuan(string $kategori, int $urutan): string
    {
        $prefixes = (array) config('laporan.kode_temuan_prefix', [
            'kritikal' => 'NC-KRT',
            'mayor'    => 'NC-MYR',
            'minor'    => 'NC-MNR',
        ]);
        $pad = (int) config('laporan.kode_temuan_padding', 3);
        $prefix = $prefixes[strtolower($kategori)] ?? 'NC-GEN';

        return sprintf('%s-%0' . $pad . 'd', $prefix, max(1, $urutan));
    }

    /**
     * Bangun konteks placeholder dari data hidup laporan & sesi audit.
     *
     * @return array<string, string>
     */
    public function buildTemplateContext(AuditLaporan $laporan): array
    {
        $laporan->loadMissing([
            'auditSesi.perusahaan',
            'auditSesi.rencana.tims',
        ]);
        $sesi = $laporan->auditSesi;
        $perusahaan = $sesi?->perusahaan;
        $rencana = $sesi?->rencana;

        $leadAuditor = $rencana?->tims?->firstWhere('peran', 'lead_auditor')?->nama
            ?: ($laporan->penanggung_jawab_nama ?: '-');

        $temuan = $sesi ? $sesi->getTemuanPerKategori() : [
            'kritikal_count' => 0,
            'mayor_count'    => 0,
            'minor_count'    => 0,
            'total_temuan'   => 0,
        ];

        $rekap = $sesi ? $sesi->getRekapPerElemen() : [];
        $skorAkhir = round((float) array_sum(array_column($rekap, 'skor_elemen')), 2);

        $dasarHukumList = MasterDasarHukum::where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get()
            ->map(fn ($dh, $idx) => ($idx + 1) . '. ' . $dh->nomor_peraturan . ' tentang ' . $dh->judul)
            ->implode("\n");

        return [
            'nama_perusahaan'        => (string) ($perusahaan?->nama_perusahaan ?? $sesi?->area_audit ?? '-'),
            'tahun_periode'          => (string) ($sesi?->tahun_periode ?? '-'),
            'tanggal_mulai'          => $sesi?->tanggal_mulai ? $sesi->tanggal_mulai->format('d/m/Y') : '-',
            'tanggal_selesai'        => $sesi?->tanggal_selesai ? $sesi->tanggal_selesai->format('d/m/Y') : '-',
            'nomor_laporan'          => (string) ($laporan->nomor_laporan ?: '-'),
            'tanggal_laporan'        => $laporan->tanggal_laporan ? $laporan->tanggal_laporan->format('d/m/Y') : '-',
            'tempat_penerbitan'      => (string) ($laporan->tempat_penerbitan ?: '-'),
            'nama_ktt'               => (string) ($perusahaan?->nama_ktt_default ?: '-'),
            'jenis_komoditas'        => (string) ($perusahaan?->jenis_komoditas ?: '-'),
            'skor_akhir'             => number_format($skorAkhir, 2, ',', '.'),
            'jumlah_temuan_kritikal' => (string) ($temuan['kritikal_count'] ?? 0),
            'jumlah_temuan_mayor'    => (string) ($temuan['mayor_count'] ?? 0),
            'jumlah_temuan_minor'    => (string) ($temuan['minor_count'] ?? 0),
            'total_temuan'           => (string) ($temuan['total_temuan'] ?? 0),
            'daftar_dasar_hukum'     => $dasarHukumList !== '' ? $dasarHukumList : '-',
            'nama_lead_auditor'      => (string) $leadAuditor,
        ];
    }

    /**
     * Bangun payload snapshot JSON kanonik yang merangkum seluruh data laporan pada saat pengesahan.
     *
     * @return array<string, mixed>
     */
    public function buildCanonicalSnapshotPayload(AuditLaporan $laporan): array
    {
        $laporan->load([
            'auditSesi.perusahaan.legalitas',
            'auditSesi.rencana.tims',
            'auditSesi.rencana.areas',
            'auditSesi.rencana.jadwals.elemen',
            'auditSesi.ofis.subElemen.elemen',
            'auditSesi.ofis.kriteria',
            'auditSesi.daftarHadirs.pesertas',
            'auditSesi.responManajemen',
            'profil',
            'peralatans',
            'narasiElemens.elemen',
            'iujps',
        ]);

        $sesi = $laporan->auditSesi;
        $perusahaan = $sesi->perusahaan;
        $legalitas = $perusahaan?->legalitas;
        $rencana = $sesi->rencana;

        $rekapElemen = $sesi->getRekapPerElemen();
        $skorAkhir = round((float) array_sum(array_column($rekapElemen, 'skor_elemen')), 2);
        $praktikTerbaik = $sesi->getSubElemenPraktekTerbaik()->values()->all();
        $temuan = $sesi->getTemuanPerKategori();

        $mapPica = function ($collection, string $kategori): array {
            $seq = 1;

            return collect($collection)->map(function ($pica) use ($kategori, &$seq) {
                $krit = $pica->auditDetail?->kriteria;
                $sub = $krit?->subElemen;
                $el = $sub?->elemen;
                $urutan = $pica->nomor_urut ? (int) $pica->nomor_urut : $seq;
                $seq++;

                return [
                    'id'                      => $pica->id,
                    'nomor_urut'              => $pica->nomor_urut,
                    'kode_temuan'             => self::formatKodeTemuan($kategori, $urutan),
                    'kategori_temuan'         => $pica->kategori_temuan,
                    'elemen_kode'             => $el?->kode_elemen,
                    'sub_elemen_kode'         => $sub?->kode_sub,
                    'kriteria_kode'           => $krit?->kode_kriteria,
                    'plor_problem'            => $pica->plor_problem,
                    'plor_location'           => $pica->plor_location,
                    'plor_objective_evidence' => $pica->plor_objective_evidence,
                    'plor_reference'          => $pica->plor_reference,
                    'deskripsi_temuan'        => $pica->deskripsi_temuan,
                    'tindakan_koreksi'        => $pica->tindakan_koreksi,
                    'pic_perbaikan'           => $pica->pic_perbaikan,
                    'tenggat_waktu'           => $pica->tenggat_waktu ? $pica->tenggat_waktu->format('Y-m-d') : null,
                    'status'                  => $pica->status,
                ];
            })->values()->all();
        };

        $context = $this->buildTemplateContext($laporan);
        $templates = MasterTemplateLaporan::where('is_active', true)->get()->keyBy('bagian');
        $override = is_array($laporan->teks_override) ? $laporan->teks_override : [];

        $renderedTemplates = [];
        foreach (array_keys(MasterTemplateLaporan::BAGIAN_OPTIONS) as $bagian) {
            if (! empty($override[$bagian])) {
                $renderedTemplates[$bagian] = (string) $override[$bagian];
            } elseif (isset($templates[$bagian])) {
                $renderedTemplates[$bagian] = $this->renderer->renderPlain($templates[$bagian]->isi_template, $context);
            } else {
                $renderedTemplates[$bagian] = '';
            }
        }

        $dasarHukum = MasterDasarHukum::where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('id')
            ->get(['kode', 'nomor_peraturan', 'judul', 'ringkasan', 'is_verified'])
            ->toArray();

        $payload = [
            'meta' => [
                'laporan_id'            => $laporan->id,
                'audit_sesi_id'         => $sesi->id,
                'revisi_ke'             => (int) $laporan->revisi_ke,
                'nomor_laporan'         => $laporan->nomor_laporan,
                'tanggal_laporan'       => $laporan->tanggal_laporan ? $laporan->tanggal_laporan->format('Y-m-d') : null,
                'tempat_penerbitan'     => $laporan->tempat_penerbitan,
                'penanggung_jawab_nama' => $laporan->penanggung_jawab_nama,
                'tahun_periode'         => (int) $sesi->tahun_periode,
                'tanggal_mulai'         => $sesi->tanggal_mulai ? $sesi->tanggal_mulai->format('Y-m-d') : null,
                'tanggal_selesai'       => $sesi->tanggal_selesai ? $sesi->tanggal_selesai->format('Y-m-d') : null,
            ],
            'perusahaan' => [
                'nama_perusahaan'       => $perusahaan?->nama_perusahaan ?? $sesi->area_audit,
                'kode_perusahaan'       => $perusahaan?->kode_perusahaan,
                'kategori'              => $perusahaan?->kategori,
                'jenis_usaha'           => $perusahaan?->jenis_usaha,
                'jenis_komoditas'       => $perusahaan?->jenis_komoditas,
                'nama_ktt_default'      => $perusahaan?->nama_ktt_default,
                'alamat_kantor_pusat'   => $perusahaan?->alamat_kantor_pusat,
                'alamat_kantor_site'    => $perusahaan?->alamat_kantor_site,
                'alamat_lokasi_tambang' => $perusahaan?->alamat_lokasi_tambang,
                'legalitas'             => $legalitas ? [
                    'jenis_izin'                            => $legalitas->jenis_izin,
                    'jenis_izin_label'                      => $legalitas->jenis_izin_label,
                    'nomor_sk_izin'                         => $legalitas->nomor_sk_izin,
                    'tanggal_sk'                            => $legalitas->tanggal_sk ? $legalitas->tanggal_sk->format('Y-m-d') : null,
                    'tanggal_mulai_berlaku'                 => $legalitas->tanggal_mulai_berlaku ? $legalitas->tanggal_mulai_berlaku->format('Y-m-d') : null,
                    'tanggal_berakhir'                      => $legalitas->tanggal_berakhir ? $legalitas->tanggal_berakhir->format('Y-m-d') : null,
                    'status_izin'                           => $legalitas->status_izin,
                    'kode_wiup'                             => $legalitas->kode_wiup,
                    'luas_ha'                               => $legalitas->luas_ha !== null ? (float) $legalitas->luas_ha : null,
                    'lokasi_desa'                           => $legalitas->lokasi_desa,
                    'lokasi_kecamatan'                      => $legalitas->lokasi_kecamatan,
                    'lokasi_kabupaten'                      => $legalitas->lokasi_kabupaten,
                    'lokasi_provinsi'                       => $legalitas->lokasi_provinsi,
                    'nomor_persetujuan_studi_kelayakan'     => $legalitas->nomor_persetujuan_studi_kelayakan,
                    'tanggal_persetujuan_studi_kelayakan'   => $legalitas->tanggal_persetujuan_studi_kelayakan ? $legalitas->tanggal_persetujuan_studi_kelayakan->format('Y-m-d') : null,
                    'nomor_persetujuan_dokumen_lingkungan'  => $legalitas->nomor_persetujuan_dokumen_lingkungan,
                    'tanggal_persetujuan_dokumen_lingkungan'=> $legalitas->tanggal_persetujuan_dokumen_lingkungan ? $legalitas->tanggal_persetujuan_dokumen_lingkungan->format('Y-m-d') : null,
                ] : null,
            ],
            'profil' => $laporan->profil ? [
                'id'                       => $laporan->profil->id,
                'kapasitas_produksi'       => $laporan->profil->kapasitas_produksi !== null ? (float) $laporan->profil->kapasitas_produksi : null,
                'realisasi_produksi'       => $laporan->profil->realisasi_produksi !== null ? (float) $laporan->profil->realisasi_produksi : null,
                'satuan_produksi'          => $laporan->profil->satuan_produksi,
                'jumlah_pekerja'           => $laporan->profil->jumlah_pekerja,
                'jumlah_pekerja_asing'     => $laporan->profil->jumlah_pekerja_asing,
                'narasi_kegiatan'          => $laporan->profil->narasi_kegiatan,
                'narasi_penerapan_smkp'    => $laporan->profil->narasi_penerapan_smkp,
                'struktur_organisasi_path' => $laporan->profil->struktur_organisasi_path,
            ] : null,
            'peralatans' => $laporan->peralatans->map(fn ($a) => [
                'kelompok'  => $a->kelompok,
                'jenis'     => $a->jenis,
                'tipe'      => $a->tipe,
                'kapasitas' => $a->kapasitas,
                'jumlah'    => (int) $a->jumlah,
            ])->values()->all(),
            'peralatan_totals' => [
                'per_kelompok' => $laporan->getTotalPeralatanPerKelompok(),
                'grand_total'  => $laporan->getTotalPeralatan(),
            ],
            'narasi_elemens' => $laporan->narasiElemens->map(fn ($ne) => [
                'elemen_id'   => $ne->elemen_id,
                'kode_elemen' => $ne->elemen?->kode_elemen,
                'nama_elemen' => $ne->elemen?->nama_elemen,
                'narasi'      => $ne->narasi,
            ])->values()->all(),
            'lingkup_areas' => $rencana ? $rencana->areas->pluck('nama_area')->values()->all() : [],
            'ruang_lingkup_teks' => $rencana?->ruang_lingkup,
            'tim_audit' => $rencana ? $rencana->tims->map(fn ($t) => [
                'nama'    => $t->nama,
                'jabatan' => $t->jabatan,
                'peran'   => $t->peran,
            ])->values()->all() : [],
            'dasar_hukum'    => $dasarHukum,
            'narasi_template'=> $renderedTemplates,
            'pencapaian' => [
                'skor_akhir'           => $skorAkhir,
                'skor_akhir_terbilang' => Terbilang::decimal($skorAkhir, 2, true),
                'rekap_elemen'         => $rekapElemen,
            ],
            'praktik_terbaik' => $praktikTerbaik,
            'temuan' => [
                'kritikal_count' => (int) ($temuan['kritikal_count'] ?? 0),
                'mayor_count'    => (int) ($temuan['mayor_count'] ?? 0),
                'minor_count'    => (int) ($temuan['minor_count'] ?? 0),
                'total_temuan'   => (int) ($temuan['total_temuan'] ?? 0),
                'kritikal'       => $mapPica($temuan['kritikal'] ?? [], 'kritikal'),
                'mayor'          => $mapPica($temuan['mayor'] ?? [], 'mayor'),
                'minor'          => $mapPica($temuan['minor'] ?? [], 'minor'),
            ],
            'ofis' => $sesi->ofis->map(fn ($o) => [
                'nomor_urut'         => $o->nomor_urut,
                'elemen_kode'        => $o->subElemen?->elemen?->kode_elemen,
                'elemen_nama'        => $o->subElemen?->elemen?->nama_elemen,
                'sub_elemen_kode'    => $o->subElemen?->kode_sub,
                'sub_elemen_nama'    => $o->subElemen?->nama_sub,
                'kriteria_kode'      => $o->kriteria?->kode_kriteria,
                'kriteria_deskripsi' => $o->kriteria?->deskripsi,
                'deskripsi'          => $o->deskripsi,
                'rekomendasi'        => $o->rekomendasi,
            ])->values()->all(),
            'lampiran' => [
                'daftar_hadir_pembukaan' => ($dhP = $sesi->daftarHadirs->firstWhere('jenis', 'pembukaan')) ? [
                    'tanggal'        => $dhP->tanggal ? $dhP->tanggal->format('Y-m-d') : null,
                    'hari'           => $dhP->nama_hari_indonesia,
                    'lokasi'         => $dhP->lokasi,
                    'judul_kegiatan' => $dhP->judul_otomatis,
                    'jumlah_peserta' => $dhP->pesertas->count(),
                    'has_scan'       => ! empty($dhP->scan_path),
                ] : null,
                'daftar_hadir_penutupan' => ($dhT = $sesi->daftarHadirs->firstWhere('jenis', 'penutupan')) ? [
                    'tanggal'        => $dhT->tanggal ? $dhT->tanggal->format('Y-m-d') : null,
                    'hari'           => $dhT->nama_hari_indonesia,
                    'lokasi'         => $dhT->lokasi,
                    'judul_kegiatan' => $dhT->judul_otomatis,
                    'jumlah_peserta' => $dhT->pesertas->count(),
                    'has_scan'       => ! empty($dhT->scan_path),
                ] : null,
                'respon_manajemen' => ($rm = $sesi->responManajemen) ? [
                    'tanggal_respon'        => $rm->tanggal_respon ? $rm->tanggal_respon->format('Y-m-d') : null,
                    'nama_penandatangan'    => $rm->nama_penandatangan,
                    'jabatan_penandatangan' => $rm->jabatan_penandatangan,
                    'isi_respon'            => $rm->isi_respon,
                    'has_dokumen'           => ! empty($rm->dokumen_path),
                ] : null,
                'iujp_eligible' => $laporan->isEligibleForIujp(),
                'iujps'         => $laporan->iujps->map(fn ($iu) => [
                    'nama_perusahaan_iujp'   => $iu->nama_perusahaan_iujp,
                    'nomor_iujp'             => $iu->nomor_iujp,
                    'bidang_usaha'           => $iu->bidang_usaha,
                    'tanggal_audit'          => $iu->tanggal_audit ? $iu->tanggal_audit->format('Y-m-d') : null,
                    'skor_pencapaian'        => $iu->skor_pencapaian !== null ? (float) $iu->skor_pencapaian : null,
                    'jumlah_temuan_kritikal' => (int) $iu->jumlah_temuan_kritikal,
                    'jumlah_temuan_mayor'    => (int) $iu->jumlah_temuan_mayor,
                    'jumlah_temuan_minor'    => (int) $iu->jumlah_temuan_minor,
                    'ringkasan'              => $iu->ringkasan,
                ])->values()->all(),
            ],
        ];

        return $this->sortRecursive($payload);
    }

    /**
     * Encode payload ke JSON kanonik dan hitung SHA-256.
     *
     * @param array<string, mixed> $payload
     * @return array{json: string, hash: string}
     */
    public function encodeAndHash(array $payload): array
    {
        $sorted = $this->sortRecursive($payload);
        $json = (string) json_encode($sorted, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $hash = hash('sha256', $json);

        return ['json' => $json, 'hash' => $hash];
    }

    /**
     * Ajukan laporan untuk ditinjau (draft -> ditinjau).
     */
    public function ajukanTinjauan(AuditLaporan $laporan, User $actor): AuditLaporan
    {
        if ($laporan->status !== 'draft') {
            throw new InvalidArgumentException('Hanya laporan berstatus Draft yang dapat diajukan untuk ditinjau.');
        }

        $old = $laporan->toArray();
        $laporan->update([
            'status'           => 'ditinjau',
            'diajukan_oleh'    => $actor->id,
            'tanggal_diajukan' => now(),
        ]);

        AuditLog::create([
            'user_id'         => $actor->id,
            'modul'           => 'Laporan Audit',
            'tindakan'        => "Mengajukan tinjauan Laporan Audit #{$laporan->id} (sesi #{$laporan->audit_sesi_id})",
            'data_lama'       => ['status' => $old['status']],
            'data_baru'       => ['status' => 'ditinjau', 'diajukan_oleh' => $actor->id],
            'waktu_perubahan' => now(),
        ]);

        return $laporan->fresh();
    }

    /**
     * Sahkan laporan (ditinjau -> disahkan) dan buat immutable snapshot JSON + SHA-256.
     */
    public function sahkan(AuditLaporan $laporan, User $actor): AuditLaporanSnapshot
    {
        if ($laporan->status !== 'ditinjau') {
            throw new InvalidArgumentException('Hanya laporan berstatus Ditinjau yang dapat disahkan.');
        }

        return DB::transaction(function () use ($laporan, $actor): AuditLaporanSnapshot {
            $oldStatus = $laporan->status;

            $laporan->update([
                'status'             => 'disahkan',
                'ditinjau_oleh'      => $laporan->ditinjau_oleh ?: $actor->id,
                'tanggal_ditinjau'   => $laporan->tanggal_ditinjau ?: now(),
                'disahkan_oleh'      => $actor->id,
                'tanggal_pengesahan' => now(),
            ]);

            $payload = $this->buildCanonicalSnapshotPayload($laporan->fresh());
            $encoded = $this->encodeAndHash($payload);

            $snapshot = AuditLaporanSnapshot::create([
                'audit_laporan_id' => $laporan->id,
                'revisi_ke'        => (int) $laporan->revisi_ke,
                'payload_json'     => $encoded['json'],
                'hash_sha256'      => $encoded['hash'],
                'dibuat_oleh'      => $actor->id,
                'created_at'       => now(),
            ]);

            AuditLog::create([
                'user_id'         => $actor->id,
                'modul'           => 'Laporan Audit',
                'tindakan'        => "Mengesahkan Laporan Audit #{$laporan->id} (revisi ke-{$laporan->revisi_ke}, SHA-256: " . substr($encoded['hash'], 0, 12) . '...)',
                'data_lama'       => ['status' => $oldStatus],
                'data_baru'       => [
                    'status'      => 'disahkan',
                    'snapshot_id' => $snapshot->id,
                    'hash_sha256' => $encoded['hash'],
                ],
                'waktu_perubahan' => now(),
            ]);

            return $snapshot;
        });
    }

    /**
     * Catat distribusi laporan dan ubah status menjadi didistribusikan bila sebelumnya disahkan.
     *
     * @param array<string, mixed> $data
     */
    public function catatDistribusi(AuditLaporan $laporan, array $data, User $actor): AuditLaporanDistribusi
    {
        if (! $laporan->isDisahkanAtauLebih()) {
            throw new InvalidArgumentException('Distribusi hanya dapat dicatat untuk laporan yang sudah Disahkan.');
        }

        return DB::transaction(function () use ($laporan, $data, $actor): AuditLaporanDistribusi {
            $dist = AuditLaporanDistribusi::create(array_merge($data, [
                'audit_laporan_id' => $laporan->id,
                'dicatat_oleh'     => $actor->id,
            ]));

            if ($laporan->status === 'disahkan') {
                $laporan->update(['status' => 'didistribusikan']);
            }

            AuditLog::create([
                'user_id'         => $actor->id,
                'modul'           => 'Distribusi Laporan Audit',
                'tindakan'        => "Mencatat distribusi Laporan Audit #{$laporan->id} kepada {$dist->penerima_nama} ({$dist->jenis_penerima})",
                'data_lama'       => null,
                'data_baru'       => $dist->toArray(),
                'waktu_perubahan' => now(),
            ]);

            return $dist;
        });
    }

    /**
     * Tandai laporan telah disampaikan/dilaporkan ke instansi (KaIT / ESDM).
     */
    public function catatPelaporan(AuditLaporan $laporan, string $tanggalDilaporkan, User $actor): AuditLaporan
    {
        if (! $laporan->isDisahkanAtauLebih()) {
            throw new InvalidArgumentException('Pelaporan hanya dapat dicatat setelah laporan Disahkan.');
        }

        $old = ['status' => $laporan->status, 'tanggal_dilaporkan' => $laporan->tanggal_dilaporkan?->format('Y-m-d')];

        $laporan->update([
            'status'             => 'dilaporkan',
            'tanggal_dilaporkan' => $tanggalDilaporkan,
        ]);

        AuditLog::create([
            'user_id'         => $actor->id,
            'modul'           => 'Laporan Audit',
            'tindakan'        => "Mencatat penyampaian Laporan Audit #{$laporan->id} pada tanggal {$tanggalDilaporkan}",
            'data_lama'       => $old,
            'data_baru'       => ['status' => 'dilaporkan', 'tanggal_dilaporkan' => $tanggalDilaporkan],
            'waktu_perubahan' => now(),
        ]);

        return $laporan->fresh();
    }

    /**
     * Buka revisi laporan (khusus Administrator): increment revisi_ke, kembali ke draft,
     * sementara seluruh snapshot sebelumnya tetap tersimpan utuh di audit_laporan_snapshots.
     */
    public function bukaRevisi(AuditLaporan $laporan, string $catatanRevisi, User $actor): AuditLaporan
    {
        if ($laporan->status === 'draft') {
            throw new InvalidArgumentException('Laporan masih berstatus Draft dan belum memerlukan revisi.');
        }

        $old = ['status' => $laporan->status, 'revisi_ke' => $laporan->revisi_ke];
        $newRevisi = (int) $laporan->revisi_ke + 1;

        $laporan->update([
            'status'         => 'draft',
            'revisi_ke'      => $newRevisi,
            'catatan_revisi' => $catatanRevisi,
        ]);

        AuditLog::create([
            'user_id'         => $actor->id,
            'modul'           => 'Laporan Audit',
            'tindakan'        => "Membuka revisi ke-{$newRevisi} Laporan Audit #{$laporan->id}: {$catatanRevisi}",
            'data_lama'       => $old,
            'data_baru'       => ['status' => 'draft', 'revisi_ke' => $newRevisi, 'catatan_revisi' => $catatanRevisi],
            'waktu_perubahan' => now(),
        ]);

        return $laporan->fresh();
    }

    /**
     * Urutkan key associative array secara rekursif agar representasi JSON deterministik.
     *
     * @param mixed $data
     * @return mixed
     */
    protected function sortRecursive(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        if (! array_is_list($data)) {
            ksort($data);
        }

        foreach ($data as $k => $v) {
            $data[$k] = $this->sortRecursive($v);
        }

        return $data;
    }
}
