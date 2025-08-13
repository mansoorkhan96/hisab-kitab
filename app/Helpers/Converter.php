<?php

namespace App\Helpers;

class Converter
{
    public static function kgsToSacksString(int|float $kgs): string
    {
        if ($kgs === 100) {
            return '1 Bori';
        }

        if ($kgs < 100) {
            return $kgs.' KGs';
        }

        return str(floor($kgs / 100))
            ->append(' Borion')
            ->when(
                ($remainingKgs = fmod($kgs, 100)) > 0,
                fn ($str) => $str
                    ->append(', ')
                    ->append($remainingKgs)
                    ->append(' KGs')
            );
    }

    public static function kgsToMunnString(null|int|float $kgs): string
    {
        if ($kgs === null) {
            return '-';
        }

        if ($kgs < 40) {
            return $kgs;
        }

        if ($kgs === 40) {
            return '1-0';
        }

        return str(floor($kgs / 40))
            ->append('-')
            ->append(fmod($kgs, 40));
    }
}
