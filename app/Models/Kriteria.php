<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kriteria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sub_elemen_id',
        'kode_kriteria',
        'deskripsi',
        'nilai_maksimal',
        'persyaratan_dokumen',
        'pedoman_nilai_0',
        'pedoman_nilai_1',
        'pedoman_nilai_2',
        'pedoman_nilai_3',
        'pedoman_nilai_4',
        'dependency_id',
        'dependency_note',
    ];

    protected $casts = [
        'nilai_maksimal' => 'integer',
        'dependency_id'  => 'integer',
    ];

    /**
     * Relationship to prerequisite Kriteria (dependency).
     */
    public function dependency()
    {
        return $this->belongsTo(Kriteria::class, 'dependency_id');
    }

    /**
     * Relationship to dependent Kriterias.
     */
    public function dependents()
    {
        return $this->hasMany(Kriteria::class, 'dependency_id');
    }

    /**
     * Relationship to SubElemen.
     */
    public function subElemen()
    {
        return $this->belongsTo(SubElemen::class, 'sub_elemen_id');
    }

    /**
     * Relationship to AuditDetail.
     */
    public function auditDetails()
    {
        return $this->hasMany(AuditDetail::class, 'kriteria_id');
    }
}
