<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AuditDetail extends Model
{
    use HasFactory;

    protected $table = 'audit_details';

    protected $fillable = [
        'audit_sesi_id',
        'kriteria_id',
        'nilai',
        'is_na',
        'catatan',
        'lampiran',
    ];

    protected $casts = [
        'nilai'  => 'integer',
        'is_na'  => 'boolean',
    ];

    /**
     * Get the attributes that should be cast (Laravel 11/12 standard).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return $this->casts;
    }

    protected $appends = ['lampiran_url', 'catatan_array', 'lampiran_array', 'lampiran_urls'];

    /**
     * Accessor for list of finding notes (supports single string, multiline, or JSON array).
     */
    public function getCatatanArrayAttribute(): array
    {
        if (empty($this->catatan)) {
            return [];
        }

        $decoded = json_decode($this->catatan, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded, fn($c) => is_string($c) && trim($c) !== ''));
        }

        $catatanStr = (string) $this->catatan;

        // If explicitly formatted with numbered findings e.g. "1. Item\n2. Item"
        if (preg_match('/^\s*1[\.\)]\s+/m', $catatanStr) && preg_match('/\r?\n\s*2[\.\)]\s+/m', $catatanStr)) {
            $splitParts = preg_split('/\r?\n(?=\s*\d+[\.\)]\s+)/', $catatanStr);
            $cleanList = [];
            foreach ($splitParts as $part) {
                $trimmed = trim($part);
                if ($trimmed !== '') {
                    $cleaned = preg_replace('/^\d+[\.\)]\s*/', '', $trimmed);
                    $cleanList[] = !empty($cleaned) ? $cleaned : $trimmed;
                }
            }
            if (!empty($cleanList)) {
                return $cleanList;
            }
        }

        return [$catatanStr];
    }

    /**
     * Accessor for list of file attachment paths.
     */
    public function getLampiranArrayAttribute(): array
    {
        if (empty($this->lampiran)) {
            return [];
        }

        $decoded = json_decode($this->lampiran, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded, fn($p) => is_string($p) && trim($p) !== ''));
        }

        return [$this->lampiran];
    }

    /**
     * Accessor for detailed attachment list with URLs and filenames.
     */
    public function getLampiranUrlsAttribute(): array
    {
        $urls = [];
        foreach ($this->lampiran_array as $path) {
            $urls[] = [
                'path' => $path,
                'url'  => route('attachments.show', ['type' => 'lampiran', 'id' => $this->id, 'filename' => basename($path)]),
                'name' => basename($path),
            ];
        }
        return $urls;
    }

    /**
     * Accessor for first attachment URL (backward compatibility).
     */
    public function getLampiranUrlAttribute(): ?string
    {
        $urls = $this->lampiran_urls;
        return !empty($urls) ? $urls[0]['url'] : null;
    }

    /**
     * Relationship to AuditSesi.
     */
    public function auditSesi(): BelongsTo
    {
        return $this->belongsTo(AuditSesi::class, 'audit_sesi_id');
    }

    /**
     * Relationship to Kriteria (including soft-deleted criteria so historic audits stay readable).
     */
    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id')->withTrashed();
    }

    /**
     * Relationship to Pica.
     */
    public function pica(): HasOne
    {
        return $this->hasOne(Pica::class, 'audit_detail_id');
    }
}
