<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubElemen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sub_elemens';

    protected $fillable = [
        'elemen_id',
        'kode_sub',
        'nama_sub',
        'nilai_maksimal',
        'is_na',
    ];

    protected $casts = [
        'nilai_maksimal' => 'float',
        'is_na'          => 'boolean',
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
     * Relationship to Elemen.
     */
    public function elemen(): BelongsTo
    {
        return $this->belongsTo(Elemen::class, 'elemen_id');
    }

    /**
     * Relationship to Kriteria.
     */
    public function kriterias(): HasMany
    {
        return $this->hasMany(Kriteria::class, 'sub_elemen_id');
    }

    /**
     * Automatically sync the SubElemen's nilai_maksimal to equal the sum of
     * all active child Kriterias' (Sub-sub Elemen) max scores.
     */
    public function syncNilaiMaksimalFromKriterias(): void
    {
        $explicitKriterias = $this->kriterias()->where('kode_kriteria', '!=', $this->kode_sub)->get();
        if ($explicitKriterias->count() > 0) {
            $sum = (float) $explicitKriterias->sum('nilai_maksimal');
            if ((float) $this->nilai_maksimal !== $sum) {
                $this->update(['nilai_maksimal' => $sum]);
            }
        }
    }

    /**
     * Ensure standalone SubElemen (without child Kriterias) has a default Kriteria,
     * so it can still be assessed in audit sessions.
     */
    public function syncDefaultKriteria(): void
    {
        $allKriterias = $this->kriterias()->get();

        if ($allKriterias->count() === 0) {
            $maxScore = (float) $this->nilai_maksimal > 0 ? (float) $this->nilai_maksimal : 4.0;
            Kriteria::create([
                'sub_elemen_id'  => $this->id,
                'kode_kriteria'  => $this->kode_sub,
                'deskripsi'      => $this->nama_sub,
                'nilai_maksimal' => $maxScore,
                'is_na'          => (bool) ($this->is_na ?? false),
            ]);
        } elseif ($allKriterias->count() === 1) {
            $singleKriteria = $allKriterias->first();
            if ($singleKriteria && $singleKriteria->kode_kriteria === $this->kode_sub) {
                $maxScore = (float) $this->nilai_maksimal > 0 ? (float) $this->nilai_maksimal : 4.0;
                $singleKriteria->update([
                    'deskripsi'      => $this->nama_sub,
                    'nilai_maksimal' => $maxScore,
                    'is_na'          => (bool) ($this->is_na ?? false),
                ]);
            }
        } elseif ($allKriterias->count() > 1) {
            // If explicit child kriterias exist (e.g. I.1.a, I.1.b), remove any auto-generated standalone kriteria (I.1)
            $standaloneKriteria = $this->kriterias()->where('kode_kriteria', $this->kode_sub)->first();
            if ($standaloneKriteria) {
                $standaloneKriteria->delete();
            }
            $this->syncNilaiMaksimalFromKriterias();
        }
    }
}
