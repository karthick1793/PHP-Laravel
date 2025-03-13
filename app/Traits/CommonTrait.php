<?php

namespace App\Traits;

use Illuminate\Support\Str;
use NumberFormatter;

trait CommonTrait
{
    public function generateUlid()
    {
        return Str::ulid();
    }

    public function seperateNumberWithCommas($number)
    {
        $fmt = new NumberFormatter($local = 'en_IN', NumberFormatter::DECIMAL);

        return $fmt->format($number);
    }

    public function convertNumberToWords($number)
    {

        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'fourty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            100000 => 'lakh',
            10000000 => 'crore',
        ];

        if (! is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convertNumberToWords only accepts numbers between -'.PHP_INT_MAX.' and '.PHP_INT_MAX,
                E_USER_WARNING
            );

            return false;
        }

        if ($number < 0) {
            return $negative.$this->convertNumberToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            [$number, $fraction] = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen.$dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds].' '.$dictionary[100];
                if ($remainder) {
                    $string .= $conjunction.$this->convertNumberToWords($remainder);
                }
                break;
            case $number < 100000:
                $thousands = ((int) ($number / 1000));
                $remainder = $number % 1000;

                $thousands = $this->convertNumberToWords($thousands);

                $string .= $thousands.' '.$dictionary[1000];
                if ($remainder) {
                    $string .= $separator.$this->convertNumberToWords($remainder);
                }
                break;
            case $number < 10000000:
                $lakhs = ((int) ($number / 100000));
                $remainder = $number % 100000;

                $lakhs = $this->convertNumberToWords($lakhs);

                $string = $lakhs.' '.$dictionary[100000];
                if ($remainder) {
                    $string .= $separator.$this->convertNumberToWords($remainder);
                }
                break;
            case $number < 1000000000:
                $crores = ((int) ($number / 10000000));
                $remainder = $number % 10000000;

                $crores = $this->convertNumberToWords($crores);

                $string = $crores.' '.$dictionary[10000000];
                if ($remainder) {
                    $string .= $separator.$this->convertNumberToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                try {
                    $string = $this->convertNumberToWords($numBaseUnits).' '.$dictionary[$baseUnit];
                } catch (\Exception $e) {
                    return 'Value too large';
                }
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convertNumberToWords($remainder);
                }
                break;
        }

        if ($fraction !== null && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    public function getLastNumberOfTheString($string)
    {
        try {
            preg_match('/\d+$/', $string, $matches);
            $lastNumber = $matches[0];

            return $lastNumber;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getWeekYear($date)
    {
        if ($date) {
            $week = date('W', strtotime($date));
            $year = date('Y', strtotime($date));

            return "wk $week, $year";
        }

        return '';
    }
}
