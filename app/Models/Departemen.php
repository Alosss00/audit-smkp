<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;

    protected $table = 'departemens';

    protected $fillable = [
        'nama_departemen',
        'kode_departemen',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship to AuditSesi.
     */
    public function auditSesis()
    {
        return $this->hasMany(AuditSesi::class, 'departemen_id');
    }

    /**
     * Relationship to User.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'departemen_id');
    }
}
