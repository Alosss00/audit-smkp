<?php

namespace App\Support;

class Terbilang
{
    private const SATUAN = [
        '',
        'satu',
        'dua',
        'tiga',
        'empat',
        'lima',
        'enam',
        'tujuh',
        'delapan',
        'sembilan',
        'sepuluh',
        'sebelas',
    ];

    /**
     * Konversi bilangan bulat (termasuk 0 dan negatif) ke bahasa Indonesia.
     */
    public static function integer(int $angka): string
    {
        if ($angka < 0) {
            return 'minus ' . self::integer(abs($angka));
        }

        if ($angka === 0) {
            return 'nol';
        }

        return trim(preg_replace('/\s+/', ' ', self::convertPositive($angka)) ?? '');
    }

    /**
     * Konversi angka desimal (misal skor akhir 82.50) ke bahasa Indonesia dengan kata "koma".
     * Contoh: 82.50 -> "delapan puluh dua koma lima nol"
     *         31.60 -> "tiga puluh satu koma enam nol"
     *         100.00 -> "seratus" (jika trimTrailingZeros = true) atau "seratus koma nol nol"
     */
    public static function decimal(float|int|string $angka, int $decimals = 2, bool $trimTrailingZeros = true): string
    {
        $numeric = (float) $angka;
        $isNegative = $numeric < 0;
        $absNumeric = abs($numeric);

        $formatted = number_format($absNumeric, $decimals, '.', '');
        [$intPartStr, $fracPartStr] = array_pad(explode('.', $formatted, 2), 2, '');

        $intPart = (int) $intPartStr;
        $result = self::integer($intPart);

        if ($trimTrailingZeros) {
            $fracPartStr = rtrim($fracPartStr, '0');
        }

        if ($fracPartStr !== '') {
            $digitWords = [];
            foreach (str_split($fracPartStr) as $ch) {
                $d = (int) $ch;
                $digitWords[] = $d === 0 ? 'nol' : self::SATUAN[$d];
            }
            $result .= ' koma ' . implode(' ', $digitWords);
        }

        return $isNegative ? 'minus ' . $result : $result;
    }

    private static function convertPositive(int $angka): string
    {
        if ($angka < 12) {
            return self::SATUAN[$angka];
        }
        if ($angka < 20) {
            return self::SATUAN[$angka - 10] . ' belas';
        }
        if ($angka < 100) {
            $puluh = intdiv($angka, 10);
            $sisa = $angka % 10;

            return self::SATUAN[$puluh] . ' puluh' . ($sisa > 0 ? ' ' . self::convertPositive($sisa) : '');
        }
        if ($angka < 200) {
            $sisa = $angka - 100;

            return 'seratus' . ($sisa > 0 ? ' ' . self::convertPositive($sisa) : '');
        }
        if ($angka < 1000) {
            $ratus = intdiv($angka, 100);
            $sisa = $angka % 100;

            return self::SATUAN[$ratus] . ' ratus' . ($sisa > 0 ? ' ' . self::convertPositive($sisa) : '');
        }
        if ($angka < 2000) {
            $sisa = $angka - 1000;

            return 'seribu' . ($sisa > 0 ? ' ' . self::convertPositive($sisa) : '');
        }
        if ($angka < 1000000) {
            $ribu = intdiv($angka, 1000);
            $sisa = $angka % 1000;

            return self::convertPositive($ribu) . ' ribu' . ($sisa > 0 ? ' ' . self::convertPositive($sisa) : '');
        }
        if ($angka < 1000000000) {
            $juta = intdiv($angka, 1000000);
            $sisa = $angka % 1000000;

            return self::convertPositive($juta) . ' juta' . ($sisa > 0 ? ' ' . self::convertPositive($sisa) : '');
        }
        if ($angka < 1000000000000) {
            $miliar = intdiv($angka, 1000000000);
            $sisa = $angka % 1000000000;

            return self::convertPositive($miliar) . ' miliar' . ($sisa > 0 ? ' ' . self::convertPositive($sisa) : '');
        }

        $triliun = intdiv($angka, 1000000000000);
        $sisa = $angka % 1000000000000;

        return self::convertPositive($triliun) . ' triliun' . ($sisa > 0 ? ' ' . self::convertPositive($sisa) : '');
    }
}
