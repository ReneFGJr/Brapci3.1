<?php
namespace App\Libraries;

class Z3950Isbn
{
    public static function normalize(string $value): string
    {
        $isbn = strtoupper(preg_replace('/[\s-]+/', '', $value));
        if (preg_match('/^\d{9}[\dX]$/', $isbn)) {
            $sum = 0;
            for ($i = 0; $i < 10; $i++) {
                $sum += ($isbn[$i] === 'X' ? 10 : (int) $isbn[$i]) * (10 - $i);
            }
            if ($sum % 11 !== 0) return '';
            $isbn = '978' . substr($isbn, 0, 9);
            $sum = 0;
            for ($i = 0; $i < 12; $i++) $sum += (int) $isbn[$i] * ($i % 2 ? 3 : 1);
            $isbn .= (10 - $sum % 10) % 10;
        }
        if (!preg_match('/^97[89]\d{10}$/', $isbn)) return '';
        $sum = 0;
        for ($i = 0; $i < 13; $i++) $sum += (int) $isbn[$i] * ($i % 2 ? 3 : 1);
        return $sum % 10 === 0 ? $isbn : '';
    }
}
