<?php


namespace Services\XUA;


use DateTime;
use XUA\Service;

class DateTimeInstance extends Service
{
    const MINUTE = 60;
    protected array $YmdHis;
    protected int $timestamp;

    // Constructor
    public function __construct(float $timestamp = null)
    {
        $this->timestamp = $timestamp ?? microtime(true);
        $YmdHis = explode('-', date('Y-m-d-H-i-s', $this->timestamp));
        $this->YmdHis = [
            'Y' => $YmdHis[0],
            'm' => $YmdHis[1],
            'd' => $YmdHis[2],
            'h' => $YmdHis[3],
            'i' => $YmdHis[4],
            's' => $YmdHis[5],
        ];

    }

    // Getters & Setters
    public function getTimestamp(): float
    {
        return $this->timestamp;
    }

    public function setTimestamp(float $timestamp): static
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    // Casts & Formats
    public static function fromNativeDateTime(DateTime $dateTime): DateTimeInstance
    {
        return new static($dateTime->getTimestamp());
    }

    public static function fromGregorianYmdHis(string $datetime): ?DateTimeInstance
    {
        $dt = date_create($datetime);
        return $dt ? static::fromNativeDateTime($dt) : null;
    }

    public static function fromJalaliYmdHis(string $datetime): ?DateTimeInstance
    {
        preg_match('/\s*([0-9]+)-([0-9]+)-([0-9]+)\s+([0-9]+):([0-9]+):([0-9]+)\s*/', $datetime, $matches);
        [, $Y, $m, $d, $H, $i, $s] = $matches;
        [$Y, $m, $d] = self::jalaliToGregorian($Y, $m, $d);
        return static::fromGregorianYmdHis("$Y-$m-$d $H:$i:$s");
    }

    public static function fromLocalYmdHis(string $datetime): ?DateTimeInstance
    {
        if (LocaleLanguage::getLocale() == LocaleLanguage::LOC_IR) {
            return self::fromJalaliYmdHis($datetime);
        } else {
            return self::fromGregorianYmdHis($datetime);
        }
    }

    public function formatGregorian(string $format, ?string $numericsLang = null): string
    {
        return PersianExpressionService::changeNumerics(date($format, $this->timestamp), $numericsLang);
    }

    public function formatJalali(string $format, ?string $numericsLang = null): string
    {
        [$Y, $m, $d] = self::gregorianToJalali($this->YmdHis['Y'], $this->YmdHis['m'], $this->YmdHis['d']);
        $H = $this->YmdHis['h'];
        $i = $this->YmdHis['i'];
        $s = $this->YmdHis['s'];
        [$O, $P, $w, $N] = explode('-', date('O-P-w-N', $this->timestamp));
        /** @var integer $w */
        $w = (($w + 1) % 7);
        $z = ($m < 7) ? (($m - 1) * 31) + $d - 1 : (($m - 7) * 30) + $d + 185;
        $L = floor(((($Y + 12) % 33) % 4) == 1);
        $b = floor(($m - 1) / 3);
        $K = floor($z * 100 / (365.24 + $L));
        $Q = $L + 364 - $z;
        $D = PersianExpressionService::JalaliExpressions($w, PersianExpressionService::JALALI_WEEK_SHORT);
        $M = PersianExpressionService::JalaliExpressions($m - 1, PersianExpressionService::JALALI_MONTH_SHORT);
        $y = $Y % 100;

        $result = '';
        for ($index = 0; $index < strlen($format); $index++) {
            $parameter = substr($format, $index, 1);
            if ($parameter == '\\') {
                $result .= substr($format, ++$index, 1);
                continue;
            }
            switch ($parameter) {
                // Parameters exactly same as Gregorian
                case 'e':
                case 'g':
                case 'h':
                case 'i':
                case 's':
                case 'u':
                case 'B':
                case 'G':
                case 'H':
                case 'I':
                case 'N':
                case 'O':
                case 'P':
                case 'T':
                case 'U':
                case 'Z':
                    $result .= date($parameter, $this->timestamp);
                    break;

                // Parameters similar to php `date` function
                case 'a':
                    $result .= ($H < 12) ? 'ق.ظ' : 'ب.ظ';
                    break;
                case 'A':
                    $result .= ($H < 12) ? 'قبل از ظهر' : 'بعد از ظهر';
                    break;
                case 'c':
                    $result .= "$Y-$m-{$d}T$H:$i:$s$P";
                    break;
                case 'd':
                    $result .= $d;
                    break;
                case 'D':
                    $result .= $D;
                    break;
                case 'F':
                    $result .= PersianExpressionService::JalaliExpressions($m - 1, PersianExpressionService::JALALI_MONTH);
                    break;
                case 'j':
                    $result .= +$d;
                    break;
                case 'l':
                    $result .= PersianExpressionService::JalaliExpressions($w, PersianExpressionService::JALALI_WEEK);
                    break;
                case 'L':
                    $result .= $L;
                    break;
                case 'm':
                    $result .= $m;
                    break;
                case 'n':
                    $result .= +$m;
                    break;
                case 'M':
                    $result .= $M;
                    break;
                case 'o':
                    // TODO check (use $N)
                    $result .= ($w > ($z + 3) and $z < 3) ? $Y - 1 : (((3 - $Q) > $w and $Q < 3) ? $Y + 1 : $Y);
                    break;
                case 'r':
                    $result .= "{$D}، $d $M $Y $H:$i:$s $O";
                    break;
                case 'S':
                    $result .= '-ام';
                    break;
                case 't':
                    $result .= ($m != 12) ? (31 - floor($m / 7)) : ($L + 29);
                    break;
                case 'w':
                    $result .= $w;
                    break;
                case 'W':
                    // TODO check (use $N)
                    $avs = (($w == 6) ? 0 : $w + 1) - ($z % 7);
                    if ($avs < 0) $avs += 7;
                    $num = floor(($z + $avs) / 7);
                    if ($avs < 4) {
                        $num++;
                    } elseif ($num < 1) {
                        $num = ($avs == 4 or $avs == ((((($Y % 33) % 4) - 2) == (floor(($Y % 33) * 0.05))) ? 5 : 4)) ? 53 : 52;
                    }
                    $aks = $avs + $L;
                    if ($aks == 7) $aks = 0;
                    $result .= (($L + 363 - $z) < $aks and $aks < 3) ? '01' : (($num < 10) ? '0' . $num : $num);
                    break;
                case 'y':
                    $result .= $y;
                    break;
                case 'Y':
                    $result .= $Y;
                    break;
                case 'z':
                    $result .= $z;
                    break;

                // Additional Parameters
                case 'b':
                    $result .= $b + 1;
                    break;
                case 'C':
                    $result .= floor(($Y + 99) / 100);
                    break;
                case 'f':
                    $result .= PersianExpressionService::JalaliExpressions($b, PersianExpressionService::JALALI_SEASON);
                    break;
                case 'J':
                    $result .= PersianExpressionService::numberToText($d);
                    break;
                case 'k';
                    $result .= 100 - $K;
                    break;
                case 'K':
                    $result .= $K;
                    break;
                case 'p':
                    $result .= PersianExpressionService::JalaliExpressions($Y % 12, PersianExpressionService::JALALI_MONTH_ANCIENT);
                    break;
                case 'q':
                    $result .= PersianExpressionService::JalaliExpressions($Y % 12, PersianExpressionService::JALALI_YEAR_ZODIAC);
                    break;
                case 'Q':
                    $result .= $Q;
                    break;
                case 'v':
                    $result .= PersianExpressionService::numberToText($y);
                    break;
                case 'V':
                    $result .= PersianExpressionService::numberToText($Y);
                    break;

                // Default
                default:
                    $result .= $parameter;
            }
        }
        return PersianExpressionService::changeNumerics($result, $numericsLang);
    }

    public function formatLocal(string $format, ?string $numericsLang = null): string
    {
        if (LocaleLanguage::getLocale() == LocaleLanguage::LOC_IR) {
            return $this->formatJalali($format, $numericsLang);
        } else {
            return $this->formatGregorian($format, $numericsLang);
        }
    }

    // Operations
    public function add(DateTimeInstance $dateTimeInstance): static
    {
        $this->timestamp += $dateTimeInstance->timestamp;
        return $this;
    }

    public function sub(DateTimeInstance $dateTimeInstance) : static
    {
        $this->timestamp -= $dateTimeInstance->timestamp;
        return $this;
    }

    public function dist(DateTimeInstance $dateTimeInstance) : static
    {
        return new static(abs($this->timestamp - $dateTimeInstance->timestamp));
    }

    // DateTime Parts: Getters & Modifiers
//    public function getYearGregorian(): int
//    {
//
//    }
//
//    public function setYearGregorian(): DateTime
//    {
//
//    }
//
//    public function addYearsGregorian(): DateTime
//    {
//
//    }

    // helpers
    protected static function gregorianToJalali(int $gY, int $gm, int $gd): array
    {
        $gdm = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        $gY2 = ($gm > 2) ? ($gY + 1) : $gY;
        $d = 355666 + (365 * $gY) + (floor(($gY2 + 3) / 4)) - (floor(($gY2 + 99) / 100)) + (floor(($gY2 + 399) / 400)) + $gd + $gdm[$gm - 1];
        $jY = -1595 + (33 * (floor($d / 12053)));
        $d %= 12053;
        $jY += 4 * (floor($d / 1461));
        $d %= 1461;
        if ($d > 365) {
            $jY += floor(($d - 1) / 365);
            $d = ($d - 1) % 365;
        }
        if ($d < 186) {
            $jm = 1 + floor($d / 31);
            $jd = 1 + ($d % 31);
        } else {
            $jm = 7 + floor(($d - 186) / 30);
            $jd = 1 + (($d - 186) % 30);
        }
        return [$jY, str_pad($jm, 2, '0', STR_PAD_LEFT), str_pad($jd, 2, '0', STR_PAD_LEFT)];
    }

    protected static function jalaliToGregorian($jY, $jm, $jd) : array
    {
        $jY += 1595;
        $days = -355668 + (365 * $jY) + (((int) ($jY / 33)) * 8) + ((int) ((($jY % 33) + 3) / 4)) + $jd + (($jm < 7) ? ($jm - 1) * 31 : (($jm - 7) * 30) + 186);
        $gY = 400 * ((int) ($days / 146097));
        $days %= 146097;
        if ($days > 36524) {
            $gY += 100 * ((int) (--$days / 36524));
            $days %= 36524;
            if ($days >= 365) $days++;
        }
        $gY += 4 * ((int) ($days / 1461));
        $days %= 1461;
        if ($days > 365) {
            $gY += (int) (($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        $gd = $days + 1;
        $sal_a = [0, 31, (($gY % 4 == 0 and $gY % 100 != 0) or ($gY % 400 == 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        for ($gm = 0; $gm < 13 and $gd > $sal_a[$gm]; $gm++) {
            $gd -= $sal_a[$gm];
        }
        return [$gY, str_pad($gm, 2, '0', STR_PAD_LEFT), str_pad($gd, 2, '0', STR_PAD_LEFT)];
    }
}