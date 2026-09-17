<?php

namespace App\Services;

use App\Models\AuditDetail;
use App\Models\AuditSesi;
use App\Models\KriteriaGatingRule;
use Illuminate\Support\Collection;

class GatingRuleService
{
    /**
     * Evaluate scoring gating rules for an audit session.
     *
     * @param  AuditSesi   $sesi
     * @param  array|null  $inputScores  Optional array of input data from request (keyed by audit_detail_id or kriteria_id)
     * @return Collection Collection of detected gating violations / warnings
     */
    public function evaluate(AuditSesi $sesi, ?array $inputScores = null): Collection
    {
        // Load active gating rules with relationships
        $rules = KriteriaGatingRule::active()
            ->with(['kriteriaHulu', 'kriteriaHilir'])
            ->get();

        if ($rules->isEmpty()) {
            return collect();
        }

        // Build a score lookup table mapped by kriteria_id: [kriteria_id => ['nilai' => float, 'is_na' => bool]]
        $scoreMap = $this->buildScoreMap($sesi, $inputScores);

        $violations = collect();

        foreach ($rules as $rule) {
            $huluId = $rule->kriteria_hulu_id;
            $hilirId = $rule->kriteria_hilir_id;

            if (!$huluId || !$hilirId) {
                continue;
            }

            $huluData = $scoreMap[$huluId] ?? null;
            $hilirData = $scoreMap[$hilirId] ?? null;

            if (!$huluData || !$hilirData) {
                continue;
            }

            // If either hulu or hilir is marked as N/A, gating is waived
            if ($huluData['is_na'] || $hilirData['is_na']) {
                continue;
            }

            $skorHulu  = (float) $huluData['nilai'];
            $skorHilir = (float) $hilirData['nilai'];

            // Determine if gating rule condition is triggered
            $isTriggered = false;
            $maxAllowedHilir = null;

            if ($rule->ambang_hulu !== null) {
                // Fixed threshold mode: triggered when upstream score <= ambang_hulu
                if ($skorHulu <= (float) $rule->ambang_hulu) {
                    $isTriggered = true;
                    $maxAllowedHilir = $rule->skor_maks_hilir !== null
                        ? (float) $rule->skor_maks_hilir
                        : $skorHulu;
                }
            } else {
                // Dynamic ceiling benchmark: downstream should not exceed upstream score
                if ($skorHilir > $skorHulu) {
                    $isTriggered = true;
                    $maxAllowedHilir = $skorHulu;
                }
            }

            // Check if downstream exceeds the allowed maximum
            if ($isTriggered && $maxAllowedHilir !== null && $skorHilir > $maxAllowedHilir) {
                $kodeHulu  = $rule->kriteriaHulu?->kode_kriteria ?? "ID:{$huluId}";
                $kodeHilir = $rule->kriteriaHilir?->kode_kriteria ?? "ID:{$hilirId}";

                $violations->push([
                    'rule_id'           => $rule->id,
                    'mode'              => $rule->mode, // 'hard_block' or 'soft_flag'
                    'kriteria_hulu_id'  => $huluId,
                    'kode_hulu'         => $kodeHulu,
                    'deskripsi_hulu'    => $rule->kriteriaHulu?->deskripsi,
                    'skor_hulu'         => $skorHulu,
                    'kriteria_hilir_id' => $hilirId,
                    'kode_hilir'        => $kodeHilir,
                    'deskripsi_hilir'   => $rule->kriteriaHilir?->deskripsi,
                    'skor_hilir'        => $skorHilir,
                    'skor_maks_hilir'   => $maxAllowedHilir,
                    'deskripsi_simpul'  => $rule->deskripsi_simpul,
                    'pesan'             => "Nilai kriteria hilir {$kodeHilir} ({$skorHilir}) melebihi batas gating ({$maxAllowedHilir}) karena kriteria hulu {$kodeHulu} bernilai {$skorHulu}.",
                ]);
            }
        }

        return $violations;
    }

    /**
     * Check if there are any hard_block violations.
     */
    public function hasHardBlockViolations(AuditSesi $sesi, ?array $inputScores = null): bool
    {
        return $this->evaluate($sesi, $inputScores)->where('mode', 'hard_block')->isNotEmpty();
    }

    /**
     * Get error messages for hard_block violations.
     */
    public function getHardBlockMessages(AuditSesi $sesi, ?array $inputScores = null): array
    {
        return $this->evaluate($sesi, $inputScores)
            ->where('mode', 'hard_block')
            ->pluck('pesan')
            ->toArray();
    }

    /**
     * Get warning messages for soft_flag violations.
     */
    public function getSoftFlagWarnings(AuditSesi $sesi, ?array $inputScores = null): array
    {
        return $this->evaluate($sesi, $inputScores)
            ->where('mode', 'soft_flag')
            ->pluck('pesan')
            ->toArray();
    }

    /**
     * Build unified score map from database details and/or request inputs.
     */
    protected function buildScoreMap(AuditSesi $sesi, ?array $inputScores = null): array
    {
        // Load details for this session
        $details = AuditDetail::where('audit_sesi_id', $sesi->id)->get();
        $map = [];

        foreach ($details as $detail) {
            $kriteriaId = (int) $detail->kriteria_id;
            $nilai      = (float) $detail->nilai;
            $isNa       = (bool) $detail->is_na;

            // If inputScores provided, override with incoming values
            if ($inputScores !== null) {
                // Check if inputScores is keyed by audit_detail_id
                if (isset($inputScores[$detail->id])) {
                    $item = $inputScores[$detail->id];
                    $isNa  = isset($item['is_na']) && (bool) $item['is_na'];
                    $nilai = $isNa ? 0.0 : (float) ($item['nilai'] ?? 0);
                } elseif (isset($inputScores[$kriteriaId])) {
                    // Or keyed by kriteria_id
                    $item = $inputScores[$kriteriaId];
                    $isNa  = isset($item['is_na']) && (bool) $item['is_na'];
                    $nilai = $isNa ? 0.0 : (float) ($item['nilai'] ?? 0);
                }
            }

            $map[$kriteriaId] = [
                'nilai' => $nilai,
                'is_na' => $isNa,
            ];
        }

        return $map;
    }
}
