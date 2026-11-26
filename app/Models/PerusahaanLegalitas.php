<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerusahaanLegalitas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'perusahaan_legalitas';

    public const JENIS_IZIN_OPTIONS = [
        'iup_eksplorasi'           => 'IUP Eksplorasi',
        'iup_operasi_produksi'     => 'IUP Operasi Produksi',
        'iupk'                     => 'IUPK',
        'iup_pengolahan_pemurnian' => 'IUP Operasi Produksi Khusus Pengolahan & Pemurnian',
        'ipr'                      => 'IPR (Izin Pertambangan Rakyat)',
        'iujp'                     => 'IUJP (Izin Usaha Jasa Pertambangan)',
        'pkp2b'                    => 'PKP2B',
        'kontrak_karya'            => 'Kontrak Karya (KK)',
        'lainnya'                  => 'Lainnya',
    ];

    protected $fillable = [
        'perusahaan_id',
        'jenis_izin',
        'nomor_sk_izin',
        'tanggal_sk',
        'tanggal_mulai_berlaku',
        'tanggal_berakhir',
        'status_izin',
        'kode_wiup',
        'luas_ha',
        'lokasi_desa',
        'lokasi_kecamatan',
        'lokasi_kabupaten',
        'lokasi_provinsi',
        'nomor_persetujuan_studi_kelayakan',
        'tanggal_persetujuan_studi_kelayakan',
        'nomor_persetujuan_dokumen_lingkungan',
        'tanggal_persetujuan_dokumen_lingkungan',
    ];

    protected $casts = [
        'tanggal_sk'                             => 'date',
        'tanggal_mulai_berlaku'                  => 'date',
        'tanggal_berakhir'                       => 'date',
        'luas_ha'                                => 'decimal:2',
        'tanggal_persetujuan_studi_kelayakan'    => 'date',
        'tanggal_persetujuan_dokumen_lingkungan' => 'date',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
    }

    public function getJenisIzinLabelAttribute(): string
    {
        return self::JENIS_IZIN_OPTIONS[$this->jenis_izin] ?? ($this->jenis_izin ?: '-');
    }
}
