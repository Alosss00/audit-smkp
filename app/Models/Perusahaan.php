<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perusahaan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'perusahaans';

    protected $fillable = [
        'nama_perusahaan',
        'kode_perusahaan',
        'kategori',
        'jenis_perizinan',
        'jenis_komoditas',
        'alamat_kantor_site',
        'alamat_lokasi_tambang',
        'alamat_kantor_pusat',
        'nama_ktt_default',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship to AuditSesi.
     */
    public function auditSesis()
    {
        return $this->hasMany(AuditSesi::class, 'perusahaan_id');
    }

    /**
     * Relationship to MasterTahapAwal (Master Data Dokumen Tahap Awal Audit per Perusahaan).
     */
    public function masterTahapAwal()
    {
        return $this->hasOne(MasterTahapAwal::class, 'perusahaan_id');
    }
}
