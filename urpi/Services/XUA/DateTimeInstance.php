<?php


namespace Services\XUA;


use DateTime;
use XUA\Service;

class DateTimeInstance extends Service
{
    protected float $timestamp;
    protected array $cache;

    // Constructor and Cache Updater
    public function __construct(float $seconds = null)
    {
        $this->timestamp = $seconds ?? microtime(true);
        $this->updateCache();
    }

    protected function updateCache(): void
    {
        $g = explode('-', date('Y-m-d-H-i-s-u', $this->timestamp));
        $j = static::gregorianToJalali($g[0], $g[1], $g[2]);
        $this->cache = [
            'yearGregorian' => $g[0],
            'monthGregorian' => $g[1],
            'dayGregorian' => $g[2],

            'yearJalali' => $j[0],
            'monthJalali' => $j[1],
            'dayJalali' => $j[2],

            'hour' => $g[3],
            'minute' => $g[4],
            'second' => $g[5],
            'microsecond' => $g[6],
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

    public static function fromGregorianString(string $format , string $datetime): ?DateTimeInstance
    {
        $dt = DateTime::createFromFormat($format, $datetime);
        return $dt ? static::fromNativeDateTime($dt) : null;
    }

    public static function fromJalaliString(string $format , string $datetime): ?DateTimeInstance
    {
        // @TODO implement
        return null;
    }

    public function formatGregorian (string $format): string
    {
        return date($format, $this->timestamp);
    }

    public function formatJalali (string $format, ?string $numericsLang = null): string
    {
        $Y = $this->cache['yearJalali'];
        $m = $this->cache['monthJalali'];
        $d = $this->cache['dayJalali'];
        $H = $this->cache['hour'];
        $i = $this->cache['minute'];
        $s = $this->cache['second'];
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
        $y = $Y % 100 ?? $Y;

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
        $gdm = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
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
}