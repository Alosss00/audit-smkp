<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterTemplateLaporan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_template_laporan';

    public const BAGIAN_OPTIONS = [
        'latar_belakang'   => 'Bab 1 — Latar Belakang',
        'tujuan_audit'     => 'Tujuan Pelaksanaan Audit Internal',
        'penutup'          => 'Bab Penutup',
        'respon_manajemen' => 'Template Respon Manajemen',
    ];

    protected $fillable = [
        'bagian',
        'judul',
        'isi_template',
        'is_active',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getBagianLabelAttribute(): string
    {
        return self::BAGIAN_OPTIONS[$this->bagian] ?? (string) $this->bagian;
    }
}
