<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false; // uses waktu_perubahan only

    protected $fillable = [
        'user_id',
        'modul',
        'tindakan',
        'data_lama',
        'data_baru',
        'waktu_perubahan',
    ];

    protected $casts = [
        'data_lama'        => 'array',
        'data_baru'        => 'array',
        'waktu_perubahan'  => 'datetime',
    ];

    /**
     * Relationship to User who performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
