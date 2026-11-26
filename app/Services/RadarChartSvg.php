<?php

namespace App\Services;

class RadarChartSvg
{
    /**
     * Render grafik radar pencapaian per elemen SMKP sebagai SVG murni di sisi server.
     * Patuh CSP ketat: tanpa JavaScript dan tanpa atribut inline style="...".
     *
     * @param array<int, array{kode_elemen?: string, nama_elemen?: string, persentase?: float|int}> $rekapElemen
     */
    public function render(array $rekapElemen, int $width = 560, int $height = 380): string
    {
        $items = array_values($rekapElemen);
        $count = count($items);
        if ($count < 3) {
            // Fallback minimal 7 sumbu kosong jika data elemen belum tersedia
            $items = [];
            foreach (['I', 'II', 'III', 'IV', 'V', 'VI', 'VII'] as $kode) {
                $items[] = [
                    'kode_elemen' => $kode,
                    'nama_elemen' => 'Elemen ' . $kode,
                    'persentase'  => 0.0,
                ];
            }
            $count = 7;
        }

        $cx = $width / 2.0;
        $cy = $height / 2.0 + 6.0;
        $radius = min($width, $height) * 0.34;
        $levels = [20, 40, 60, 80, 100];

        $svg = [];
        $svg[] = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $width . ' ' . $height . '" width="' . $width . '" height="' . $height . '" role="img" aria-label="Grafik Radar Tingkat Pencapaian SMKP">';
        $svg[] = '<rect x="0" y="0" width="' . $width . '" height="' . $height . '" fill="#ffffff" stroke="#cbd5e1" stroke-width="1" rx="8"/>';

        // Grid poligon konsentris (20%, 40%, 60%, 80%, 100%)
        foreach ($levels as $lvl) {
            $r = $radius * ($lvl / 100.0);
            $pts = [];
            for ($i = 0; $i < $count; $i++) {
                $angle = -M_PI / 2 + (2 * M_PI * $i / $count);
                $x = $cx + $r * cos($angle);
                $y = $cy + $r * sin($angle);
                $pts[] = sprintf('%.2f,%.2f', $x, $y);
            }
            $strokeColor = $lvl === 100 ? '#64748b' : '#cbd5e1';
            $svg[] = '<polygon points="' . implode(' ', $pts) . '" fill="none" stroke="' . $strokeColor . '" stroke-width="1"/>';
            $svg[] = '<text x="' . sprintf('%.2f', $cx + 4) . '" y="' . sprintf('%.2f', $cy - $r + 11) . '" font-family="Arial, Helvetica, sans-serif" font-size="9" fill="#64748b">' . $lvl . '%</text>';
        }

        // Garis sumbu & label elemen
        $dataPoints = [];
        $dotMarkup = [];
        for ($i = 0; $i < $count; $i++) {
            $angle = -M_PI / 2 + (2 * M_PI * $i / $count);
            $cosA = cos($angle);
            $sinA = sin($angle);

            $xAxis = $cx + $radius * $cosA;
            $yAxis = $cy + $radius * $sinA;
            $svg[] = '<line x1="' . sprintf('%.2f', $cx) . '" y1="' . sprintf('%.2f', $cy) . '" x2="' . sprintf('%.2f', $xAxis) . '" y2="' . sprintf('%.2f', $yAxis) . '" stroke="#cbd5e1" stroke-width="1"/>';

            $pct = max(0.0, min(100.0, (float) ($items[$i]['persentase'] ?? 0.0)));
            $rVal = $radius * ($pct / 100.0);
            $xVal = $cx + $rVal * $cosA;
            $yVal = $cy + $rVal * $sinA;
            $dataPoints[] = sprintf('%.2f,%.2f', $xVal, $yVal);

            $dotMarkup[] = '<circle cx="' . sprintf('%.2f', $xVal) . '" cy="' . sprintf('%.2f', $yVal) . '" r="3.5" fill="#0284c7" stroke="#ffffff" stroke-width="1.2"/>';

            // Posisi label elemen di luar lingkaran terluar
            $labelRadius = $radius + 24.0;
            $xLbl = $cx + $labelRadius * $cosA;
            $yLbl = $cy + $labelRadius * $sinA;

            $anchor = 'middle';
            if ($cosA > 0.25) {
                $anchor = 'start';
            } elseif ($cosA < -0.25) {
                $anchor = 'end';
            }

            $kode = (string) ($items[$i]['kode_elemen'] ?? ($i + 1));
            $pctStr = number_format($pct, 2, ',', '.') . '%';

            $svg[] = '<text x="' . sprintf('%.2f', $xLbl) . '" y="' . sprintf('%.2f', $yLbl) . '" text-anchor="' . $anchor . '" font-family="Arial, Helvetica, sans-serif" font-size="11" font-weight="bold" fill="#0f172a">Elemen ' . e($kode) . '</text>';
            $svg[] = '<text x="' . sprintf('%.2f', $xLbl) . '" y="' . sprintf('%.2f', $yLbl + 13) . '" text-anchor="' . $anchor . '" font-family="Arial, Helvetica, sans-serif" font-size="10" fill="#0369a1">(' . e($pctStr) . ')</text>';
        }

        // Poligon pencapaian aktual
        $svg[] = '<polygon points="' . implode(' ', $dataPoints) . '" fill="#0284c7" fill-opacity="0.22" stroke="#0284c7" stroke-width="2.2"/>';

        foreach ($dotMarkup as $dot) {
            $svg[] = $dot;
        }

        $svg[] = '</svg>';

        return implode("\n", $svg);
    }
}
