<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestorePoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kode',
        'nama',
        'deskripsi',
        'tipe',
        'file_path',
        'file_size',
        'table_counts',
        'checksum',
    ];

    protected $casts = [
        'table_counts' => 'array',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /**
     * User who created this restore point.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
