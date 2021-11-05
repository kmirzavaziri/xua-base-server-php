<?php

namespace Xua\Core\Services;

use Xua\Core\Eves\Service;

abstract class PersianExpressionService extends Service
{
    const NUMERIC_MAP = [
        'fa' => [
            '0' => '۰',
            '1' => '۱',
            '2' => '۲',
            '3' => '۳',
            '4' => '۴',
            '5' => '۵',
            '6' => '۶',
            '7' => '۷',
            '8' => '۸',
            '9' => '۹',

            '٤' => '۴',
            '٥' => '۵',
            '٦' => '۶',
        ],
        'en' => [
            '۰' => '0',
            '۱' => '1',
            '۲' => '2',
            '۳' => '3',
            '۴' => '4',
            '۵' => '5',
            '۶' => '6',
            '۷' => '7',
            '۸' => '8',
            '۹' => '9',

            '٤' => '4',
            '٥' => '5',
            '٦' => '6',
        ],
    ];

    const JALALI_YEAR_ZODIAC = 'jalali_year_zodiac';
    const JALALI_SEASON = 'jalali_season';
    const JALALI_MONTH = 'jalali_month';
    const JALALI_MONTH_SHORT = 'jalali_month_short';
    const JALALI_MONTH_ANCIENT = 'jalali_month_ancient';
    const JALALI_WEEK = 'jalali_week';
    const JALALI_WEEK_SHORT = 'jalali_week_short';

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

    public static function JalaliExpressions(int $number, string $type) : string
    {
        return match ($type) {
            self::JALALI_YEAR_ZODIAC => ['مار', 'اسب', 'گوسفند', 'میمون', 'مرغ', 'سگ', 'خوک', 'موش', 'گاو', 'پلنگ', 'خرگوش', 'نهنگ'][$number],
            self::JALALI_SEASON => ['بهار', 'تابستان', 'پاییز', 'زمستان'][$number],
            self::JALALI_MONTH => ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'][$number],
            self::JALALI_MONTH_SHORT => ['فرو', 'ارد', 'خرد', 'تیر', 'امر', 'شهر', 'مهر', 'آبا', 'آذر', 'دی', 'بهم‍', 'اسف‍'][$number],
            self::JALALI_MONTH_ANCIENT => ['حمل', 'ثور', 'جوزا', 'سرطان', 'اسد', 'سنبله', 'میزان', 'عقرب', 'قوس', 'جدی', 'دلو', 'حوت'][$number],
            self::JALALI_WEEK => ['شنبه', 'یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'][$number],
            self::JALALI_WEEK_SHORT => ['شنب‍', 'یکش‍', 'دوش‍', 'سه‌ش‍', 'چها', 'پنج‍', 'جمع‍'][$number],
            default => $number,
        };

    }

    public static function changeNumerics(string $text, ?string $numericsLang = null): string
    {
        $numericsLang = $numericsLang ?? ConstantService::DEFAULT_LANG;
        $result = '';
        for ($i = 0; $i < mb_strlen($text); $i++) {
            $t = mb_substr($text, $i, 1);
            $result .= self::NUMERIC_MAP[$numericsLang][$t] ?? $t;
        }
        return $result;
    }

}