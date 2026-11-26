<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPenandatanganRencana extends Model
{
    use HasFactory;

    protected $table = 'master_penandatangan_rencana';

    protected $fillable = [
        'kelompok',
        'urutan',
        'jabatan',
        'is_active',
    ];

    protected $casts = [
        'urutan'    => 'integer',
        'is_active' => 'boolean',
    ];

    protected function casts(): array
    {
        return $this->casts;
    }
}
