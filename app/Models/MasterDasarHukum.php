<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterDasarHukum extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_dasar_hukum';

    protected $fillable = [
        'kode',
        'nomor_peraturan',
        'judul',
        'ringkasan',
        'urutan',
        'is_active',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'urutan'      => 'integer',
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
}
