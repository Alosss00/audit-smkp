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
}
