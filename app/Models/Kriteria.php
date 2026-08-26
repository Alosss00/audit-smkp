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
        'pedoman_nilai_json',
        'dependency_id',
        'dependency_note',
        'is_na',
    ];

    protected $casts = [
        'nilai_maksimal'     => 'float',
        'dependency_id'      => 'integer',
        'is_na'              => 'boolean',
        'pedoman_nilai_json' => 'array',
    ];

    protected $appends = ['pedoman_array'];

    /**
     * Get unified rubric guidelines array (keyed by score 0..N).
     */
    public function getPedomanArrayAttribute(): array
    {
        if (!empty($this->pedoman_nilai_json) && is_array($this->pedoman_nilai_json)) {
            return $this->pedoman_nilai_json;
        }

        return [
            '0' => $this->pedoman_nilai_0 ?? 'Nilai 0: Tidak ada dokumen, tidak dilaksanakan, dan tidak ada bukti fisik.',
            '1' => $this->pedoman_nilai_1 ?? 'Nilai 1: Ada draft/wacana tetapi belum disahkan atau belum disosialisasikan.',
            '2' => $this->pedoman_nilai_2 ?? 'Nilai 2: Terdokumentasi secara resmi tetapi penerapan di lapangan masih terbatas.',
            '3' => $this->pedoman_nilai_3 ?? 'Nilai 3: Terdokumentasi dan diterapkan penuh tetapi belum dievaluasi secara berkala.',
            '4' => $this->pedoman_nilai_4 ?? 'Nilai 4: Terdokumentasi resmi, diterapkan 100%, dievaluasi berkala, dan ditindaklanjuti.',
        ];
    }

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
