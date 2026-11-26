<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterFaktorPenyesuaian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_faktor_penyesuaian';

    protected $fillable = [
        'kode',
        'nama',
        'tipe',
        'nilai_hari',
        'urutan',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'nilai_hari' => 'decimal:1',
        'urutan'     => 'integer',
        'is_active'  => 'boolean',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }
}
