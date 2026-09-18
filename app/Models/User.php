<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'area',
        'departemen_id',
        'is_active',
    ];

    /**
     * Relationship to Departemen.
     */
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship to AuditSesi.
     */
    public function auditSesis()
    {
        return $this->hasMany(AuditSesi::class);
    }

    /**
     * Helper to check if user is Administrator (Full Access).
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Helper to check if user is Auditor SMKP (Dashboard & Penilaian Access).
     */
    public function isAuditorSmkp(): bool
    {
        return $this->role === 'auditor_smkp';
    }

    /**
     * Helper to check if user is Auditor Perusahaan (Auditee / Area Scoped).
     */
    public function isAuditor(): bool
    {
        return $this->role === 'auditor';
    }

    /**
     * Helper to check if user has access to Penilaian & Monitoring.
     */
    public function hasPenilaianAccess(): bool
    {
        return in_array($this->role, ['admin', 'auditor_smkp']);
    }

    /**
     * Helper to check if user has access to Master Data & Admin configurations.
     */
    public function hasMasterDataAccess(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get human-readable role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin'        => 'Administrator',
            'auditor_smkp' => 'Auditor SMKP',
            'auditor'      => 'Auditor Perusahaan',
            default        => ucfirst($this->role),
        };
    }
}
