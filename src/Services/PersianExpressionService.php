<?php

namespace Xua\Core\Services;

use Xua\Core\Eves\Service;

abstract class PersianExpressionService extends Service
{
    public static function numberToText(int $number) : string
    {
        if (!$number) {
            return 'صفر';
        }

        $result = '';
        if ($number < 0) {
            $result = 'منهای ';
            $number = -$number;
        }

        $dividableLength = ceil(strlen($number) / 3) * 3;
        $number = str_pad($number, $dividableLength, '0', STR_PAD_LEFT);
        $parts = str_split($number, 3);
        $partsLength = count($parts);

        $partTexts = [];
        foreach ($parts as $i => $part) {
            $partLabel = ['', 'هزار', 'میلیون', 'میلیارد'][$partsLength - $i - 1];

            if ($part == 0) {
                continue;
            } elseif ($part == 1) {
                $tmp = $partLabel ? '' : 'یک';
            } else {
                $hundredsDigit = substr($part, 0, 1);
                $tensDigit = substr($part, 1, 1);
                $onesDigit = substr($part, 2, 1);
                $tmp =
                    ['', 'صد', 'دویست', 'سیصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد'][$hundredsDigit] .
                    (($hundredsDigit and ($tensDigit or $onesDigit)) ? ' و ' : '') .
                    ($tensDigit == 1
                        ?
                        ['ده', 'یازده', 'دوازده', 'سیزده', 'چهارده', 'پانزده', 'شانزده', 'هفده', 'هجده', 'نوزده'][$onesDigit]
                        :
                        ['', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود'][$tensDigit] .
                        (($tensDigit and $onesDigit) ? ' و ' : '') .
                        ['', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'][$onesDigit]
                    );
            }

            $partTexts[] =
                $tmp .
                (($partLabel and $tmp) ? ' ' : '') .
                $partLabel;
        }

        $result .= implode(' و ', $partTexts);

        return $result;
    }
}