<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * CATATAN INTEGRASI:
 * Tambahkan dua method relasi di bawah ini ke dalam file
 * app/Models/Kriteria.php yang sudah ada.
 * HAPUS kolom 'dependency_id' dan 'dependency_note' dari $fillable dan $casts.
 * HAPUS method dependency() dan dependents() yang lama.
 */
class Kriteria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sub_elemen_id',
        'kode_kriteria',
        'deskripsi',
        'nilai_maksimal',
        // 'dependency_id'   <-- HAPUS ini
        // 'dependency_note' <-- HAPUS ini
        'persyaratan_dokumen',
        'pedoman_nilai_0',
        'pedoman_nilai_1',
        'pedoman_nilai_2',
        'pedoman_nilai_3',
        'pedoman_nilai_4',
        'pedoman_nilai_json',
        'is_na',
    ];

    protected $casts = [
        'nilai_maksimal'     => 'float',
        // 'dependency_id'  => 'integer',  <-- HAPUS ini
        'is_na'              => 'boolean',
        'pedoman_nilai_json' => 'array',
    ];

    protected $appends = ['pedoman_array'];

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

    // -- HAPUS dua method lama ini ---------------------------------------------
    // public function dependency() { ... }
    // public function dependents() { ... }

    // -- TAMBAHKAN dua method baru ini sebagai gantinya ------------------------

    /**
     * Semua relasi yang BERASAL dari kriteria ini (outgoing).
     * Ini mencakup kedua jenis: 'kunci' dan 'referensi'.
     */
    public function relasiKeluar(): HasMany
    {
        return $this->hasMany(KriteriaRelasi::class, 'kriteria_asal_kode', 'kode_kriteria');
    }

    /**
     * Relasi KUNCI yang berasal dari kriteria ini.
     * Digunakan untuk memuat data banner peringatan di halaman matrix.
     */
    public function relasiKunci(): HasMany
    {
        return $this->hasMany(KriteriaRelasi::class, 'kriteria_asal_kode', 'kode_kriteria')
            ->where('jenis_relasi', 'kunci');
    }

    /**
     * Semua relasi REFERENSI yang berasal dari kriteria ini.
     * Digunakan untuk memuat data popover tooltip di halaman matrix.
     */
    public function relasiReferensi(): HasMany
    {
        return $this->hasMany(KriteriaRelasi::class, 'kriteria_asal_kode', 'kode_kriteria')
            ->where('jenis_relasi', 'referensi');
    }

    // -- Relasi lainnya yang TIDAK berubah -------------------------------------

    public function subElemen()
    {
        return $this->belongsTo(SubElemen::class, 'sub_elemen_id');
    }

    public function auditDetails()
    {
        return $this->hasMany(AuditDetail::class, 'kriteria_id');
    }

    public function gatingRulesAsHulu()
    {
        return $this->hasMany(KriteriaGatingRule::class, 'kriteria_hulu_id');
    }

    public function gatingRulesAsHilir()
    {
        return $this->hasMany(KriteriaGatingRule::class, 'kriteria_hilir_id');
    }
}
