<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'ip_address',
        'user_agent',
        'device',
        'status',
        'keterangan',
        'login_at',
        'logout_at',
    ];

    protected $casts = [
        'login_at'  => 'datetime',
        'logout_at' => 'datetime',
    ];

    /**
     * Relationship to the user who performed the login attempt.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for successful logins.
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed logins.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for timeout / auto-logout.
     */
    public function scopeTimeout($query)
    {
        return $query->where('status', 'timeout');
    }

    /**
     * Parse User-Agent into human-readable device and browser name.
     */
    public static function parseDevice(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'Tidak Diketahui';
        }

        // Detect OS
        $os = 'Unknown OS';
        if (preg_match('/windows nt 10/i', $userAgent))     $os = 'Windows 10/11';
        elseif (preg_match('/windows nt 6\.3/i', $userAgent)) $os = 'Windows 8.1';
        elseif (preg_match('/windows nt 6\.2/i', $userAgent)) $os = 'Windows 8';
        elseif (preg_match('/windows nt 6\.1/i', $userAgent)) $os = 'Windows 7';
        elseif (preg_match('/windows nt/i', $userAgent))      $os = 'Windows';
        elseif (preg_match('/macintosh|mac os x/i', $userAgent)) $os = 'macOS';
        elseif (preg_match('/android/i', $userAgent))         $os = 'Android';
        elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) $os = 'iOS';
        elseif (preg_match('/linux/i', $userAgent))           $os = 'Linux';

        // Detect Browser
        $browser = 'Browser';
        if (preg_match('/edg/i', $userAgent)) {
            $browser = 'MS Edge';
        } elseif (preg_match('/chrome|crios/i', $userAgent) && !preg_match('/opr|opera/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/firefox|fxios/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome|crios/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/opr|opera/i', $userAgent)) {
            $browser = 'Opera';
        }

        return "{$os} · {$browser}";
    }

    /**
     * Get HTML status badge.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'success' => '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1.5"><i class="bi bi-check-circle-fill me-1"></i>Login Berhasil</span>',
            'failed'  => '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2.5 py-1.5"><i class="bi bi-x-circle-fill me-1"></i>Login Gagal</span>',
            'timeout' => '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1.5"><i class="bi bi-hourglass-bottom me-1"></i>Auto-Logout (Idle)</span>',
            'logout'  => '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2.5 py-1.5"><i class="bi bi-box-arrow-right me-1"></i>Logout Manual</span>',
            default   => '<span class="badge bg-light text-dark border rounded-pill px-2.5 py-1.5">' . e(ucfirst($this->status)) . '</span>',
        };
    }

    /**
     * Get human-readable session duration.
     */
    public function getDurationAttribute(): string
    {
        if ($this->status === 'failed') {
            return '—';
        }

        if ($this->login_at && $this->logout_at) {
            $diffMinutes = $this->login_at->diffInMinutes($this->logout_at);
            if ($diffMinutes < 1) {
                $diffSeconds = $this->login_at->diffInSeconds($this->logout_at);
                return "{$diffSeconds} detik";
            }
            if ($diffMinutes >= 60) {
                $hours = floor($diffMinutes / 60);
                $remainingMinutes = $diffMinutes % 60;
                return "{$hours} jam {$remainingMinutes} menit";
            }
            return "{$diffMinutes} menit";
        }

        if ($this->status === 'success' && !$this->logout_at) {
            return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-0.5"><i class="bi bi-broadcast me-1"></i>Sesi Aktif</span>';
        }

        return '—';
    }
}
