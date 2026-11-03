<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KriteriaGatingRule extends Model
{
    use HasFactory;

    protected $table = 'kriteria_gating_rules';

    protected $fillable = [
        'kriteria_hulu_id',
        'kriteria_hilir_id',
        'ambang_hulu',
        'skor_maks_hilir',
        'mode',
        'deskripsi_simpul',
        'is_active',
    ];

    protected $casts = [
        'ambang_hulu'     => 'float',
        'skor_maks_hilir' => 'float',
        'is_active'       => 'boolean',
    ];

    /**
     * Relationship to upstream/prerequisite Kriteria (Hulu).
     */
    public function kriteriaHulu(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_hulu_id');
    }

    /**
     * Relationship to downstream/dependent Kriteria (Hilir).
     */
    public function kriteriaHilir(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_hilir_id');
    }

    /**
     * Scope for active gating rules.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for hard_block mode rules.
     */
    public function scopeHardBlock(Builder $query): Builder
    {
        return $query->where('mode', 'hard_block');
    }

    /**
     * Scope for soft_flag mode rules.
     */
    public function scopeSoftFlag(Builder $query): Builder
    {
        return $query->where('mode', 'soft_flag');
    }
}
