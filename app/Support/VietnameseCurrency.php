<?php

namespace App\Support;

/**
 * Đọc số tiền VNĐ (số nguyên) thành chữ tiếng Việt.
 */
class VietnameseCurrency
{
    private const DIGITS = ['không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];

    private const SCALES = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ', 'triệu tỷ'];

    /**
     * @param  int|float|string  $amount  Số tiền VNĐ (làm tròn về số nguyên)
     */
    public static function toWords(int|float|string $amount): string
    {
        $n = (int) round((float) $amount);
        if ($n === 0) {
            return 'Không đồng';
        }
        if ($n < 0) {
            return 'Âm '.self::toWords(-$n);
        }

        $chunks = [];
        while ($n > 0) {
            $chunks[] = $n % 1000;
            $n = intdiv($n, 1000);
        }

        $parts = [];
        for ($i = count($chunks) - 1; $i >= 0; $i--) {
            $val = $chunks[$i];
            $scaleIdx = $i;
            if ($val === 0) {
                continue;
            }
            $chunkText = self::readTriple($val, $i < count($chunks) - 1);
            $scale = self::SCALES[$scaleIdx] ?? '';
            $parts[] = trim($chunkText.($scale !== '' ? ' '.$scale : ''));
        }

        $s = implode(' ', $parts);

        return self::mbUcfirst(trim($s)).' đồng';
    }

    /**
     * Đọc một nhóm 0–999.
     */
    private static function readTriple(int $n, bool $hasHigherGroup): string
    {
        if ($n === 0) {
            return '';
        }
        $h = intdiv($n, 100);
        $rest = $n % 100;
        $t = intdiv($rest, 10);
        $u = $rest % 10;

        $out = [];

        if ($h > 0) {
            $out[] = self::DIGITS[$h].' trăm';
        } elseif ($hasHigherGroup && ($t > 0 || $u > 0)) {
            $out[] = 'không trăm';
        }

        if ($t === 0) {
            if ($u > 0) {
                if ($h > 0 || $hasHigherGroup) {
                    $out[] = 'lẻ';
                }
                $out[] = self::DIGITS[$u];
            }
        } elseif ($t === 1) {
            $out[] = 'mười';
            if ($u === 1) {
                $out[] = 'một';
            } elseif ($u === 5) {
                $out[] = 'lăm';
            } elseif ($u > 0) {
                $out[] = self::DIGITS[$u];
            }
        } else {
            $out[] = self::DIGITS[$t].' mươi';
            if ($u === 1) {
                $out[] = 'mốt';
            } elseif ($u === 4) {
                $out[] = 'tư';
            } elseif ($u === 5) {
                $out[] = 'lăm';
            } elseif ($u > 0) {
                $out[] = self::DIGITS[$u];
            }
        }

        return implode(' ', $out);
    }

    private static function mbUcfirst(string $s): string
    {
        if ($s === '') {
            return $s;
        }
        $first = mb_substr($s, 0, 1, 'UTF-8');
        $rest = mb_substr($s, 1, null, 'UTF-8');

        return mb_strtoupper($first, 'UTF-8').$rest;
    }
}
