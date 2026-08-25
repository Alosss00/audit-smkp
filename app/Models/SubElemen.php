<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
     * Relationship to Elemen.
     */
    public function elemen()
    {
        return $this->belongsTo(Elemen::class, 'elemen_id');
    }

    /**
     * Relationship to Kriteria.
     */
    public function kriterias()
    {
        return $this->hasMany(Kriteria::class, 'sub_elemen_id');
    }

    /**
     * Automatically sync the SubElemen's nilai_maksimal to equal the sum of
     * all active child Kriterias' (Sub-sub Elemen) max scores.
     */
    public function syncNilaiMaksimalFromKriterias(): void
    {
        if ($this->kriterias()->count() > 0) {
            $sum = (float) $this->kriterias()->sum('nilai_maksimal');
            if ((float) $this->nilai_maksimal !== $sum) {
                $this->update(['nilai_maksimal' => $sum]);
            }
        }
    }
}
