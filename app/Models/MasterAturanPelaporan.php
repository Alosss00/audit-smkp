<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterAturanPelaporan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_aturan_pelaporan';

    protected $fillable = [
        'kode',
        'nama_aturan',
        'hari_setelah_akhir_periode',
        'keterangan',
        'is_active',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'hari_setelah_akhir_periode' => 'integer',
        'is_active'                  => 'boolean',
        'is_verified'                => 'boolean',
        'verified_at'                => 'datetime',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
