<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditDaftarHadir extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'audit_daftar_hadirs';

    public const JENIS_OPTIONS = [
        'pembukaan' => 'Pembukaan Audit Internal SMKP',
        'penutupan' => 'Penutupan Audit Internal SMKP',
    ];

    private const HARI_INDONESIA = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];

    protected $fillable = [
        'audit_sesi_id',
        'jenis',
        'tanggal',
        'lokasi',
        'judul_kegiatan',
        'scan_path',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function auditSesi(): BelongsTo
    {
        return $this->belongsTo(AuditSesi::class, 'audit_sesi_id');
    }

    public function pesertas(): HasMany
    {
        return $this->hasMany(AuditDaftarHadirPeserta::class, 'audit_daftar_hadir_id')
            ->orderBy('urutan')
            ->orderBy('id');
    }

    /**
     * Judul kegiatan otomatis berdasarkan jenis (mencegah salah judul Pembukaan pada Penutupan).
     */
    public function getJudulOtomatisAttribute(): string
    {
        return self::JENIS_OPTIONS[$this->jenis] ?? 'Kegiatan Audit Internal SMKP';
    }

    /**
     * Nama hari Indonesia otomatis dari tanggal rapat.
     */
    public function getNamaHariIndonesiaAttribute(): string
    {
        if (! $this->tanggal) {
            return '-';
        }

        return self::HARI_INDONESIA[(int) $this->tanggal->dayOfWeek] ?? '-';
    }

    public function getScanUrlAttribute(): ?string
    {
        if ($this->scan_path) {
            return route('attachments.show', ['type' => 'daftar_hadir_scan', 'id' => $this->id]);
        }

        return null;
    }

    /**
     * Peringatan bila tanggal daftar hadir berada di luar rentang tanggal sesi audit.
     */
    public function getDateWarning(AuditSesi $sesi): ?string
    {
        if (! $this->tanggal) {
            return null;
        }

        if ($sesi->tanggal_mulai && $this->tanggal->lt($sesi->tanggal_mulai)) {
            return 'Tanggal daftar hadir (' . $this->tanggal->format('d/m/Y') . ') lebih awal dari tanggal mulai sesi audit (' . $sesi->tanggal_mulai->format('d/m/Y') . ').';
        }

        if ($sesi->tanggal_selesai && $this->tanggal->gt($sesi->tanggal_selesai)) {
            return 'Tanggal daftar hadir (' . $this->tanggal->format('d/m/Y') . ') melewati tanggal selesai sesi audit (' . $sesi->tanggal_selesai->format('d/m/Y') . ').';
        }

        return null;
    }
}
