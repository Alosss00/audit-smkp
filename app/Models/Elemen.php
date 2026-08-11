<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Elemen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'elemens';

    protected $fillable = [
        'kode_elemen',
        'nama_elemen',
        'bobot',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
    ];

    /**
     * Relationship to SubElemen.
     */
    public function subElemens()
    {
        return $this->hasMany(SubElemen::class, 'elemen_id');
    }
}
