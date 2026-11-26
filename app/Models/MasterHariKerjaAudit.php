<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterHariKerjaAudit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_hari_kerja_audit';

    protected $fillable = [
        'jumlah_min',
        'jumlah_max',
        'hari_tinggi',
        'hari_menengah',
        'hari_rendah',
        'is_verified',
        'sumber',
    ];

    protected $casts = [
        'jumlah_min'    => 'integer',
        'jumlah_max'    => 'integer',
        'hari_tinggi'   => 'decimal:1',
        'hari_menengah' => 'decimal:1',
        'hari_rendah'   => 'decimal:1',
        'is_verified'   => 'boolean',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    /**
     * Format human-readable worker count range label.
     */
    public function getRentangLabelAttribute(): string
    {
        if ($this->jumlah_max === null) {
            return '≥ ' . number_format($this->jumlah_min, 0, ',', '.') . ' pekerja';
        }

        return number_format($this->jumlah_min, 0, ',', '.') . ' – ' . number_format($this->jumlah_max, 0, ',', '.') . ' pekerja';
    }
}
