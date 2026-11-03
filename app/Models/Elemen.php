<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Elemen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'elemens';

    protected $fillable = [
        'kode_elemen',
        'nama_elemen',
        'bobot',
        'total_nilai_sub_elemen',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
        'total_nilai_sub_elemen' => 'float',
    ];

    /**
     * Get the attributes that should be cast (Laravel 11/12 standard).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return $this->casts;
    }

    /**
     * Get total accumulated max score of all active sub-elemens under this elemen.
     */
    public function getTotalNilaiSubTerpakaiAttribute(): float
    {
        return (float) $this->subElemens->sum('nilai_maksimal');
    }

    /**
     * Relationship to SubElemen.
     */
    public function subElemens(): HasMany
    {
        return $this->hasMany(SubElemen::class, 'elemen_id');
    }
}
