<?php

namespace Xua\Core\Services;

use DateTime;
use DateTimeZone;
use Xua\Core\Eves\Service;

class DateTimeInstance extends Service
{
    const MINUTE = 60;
    protected array $YmdHis;
    protected int $timestamp;

    // Constructor
    public function __construct(float $timestamp = null)
    {
        $this->timestamp = $timestamp ?? time();
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

    public static function fromGregorianYmd(string $datetime): ?DateTimeInstance
    {
        return static::fromGregorianYmdHis($datetime . ' 00:00:00');
    }

    public static function fromJalaliYmdHis(string $datetime): ?DateTimeInstance
    {
        preg_match('/\s*([0-9]+)-([0-9]+)-([0-9]+)\s+([0-9]+):([0-9]+):([0-9]+)\s*/', $datetime, $matches);
        if (count($matches) != 7) {
            return null;
        }
        [, $Y, $m, $d, $H, $i, $s] = $matches;
        [$Y, $m, $d] = self::jalaliToGregorian($Y, $m, $d);
        return static::fromGregorianYmdHis("$Y-$m-$d $H:$i:$s");
    }

    public static function fromYmdHis(string $datetime): ?DateTimeInstance
    {
        return match (LocaleLanguage::getCalendar()) {
            LocaleLanguage::CAL_JALALI => self::fromJalaliYmdHis($datetime),
            LocaleLanguage::CAL_GREGORIAN => self::fromGregorianYmdHis($datetime),
            default => self::fromGregorianYmdHis($datetime),
        };
    }

    public static function fromYmd(string $datetime): ?DateTimeInstance
    {
        return self::fromYmdHis($datetime . ' 00:00:00');
    }

    public function formatGregorian(string $format, ?string $timezone = null, ?string $lang = null): string
    {
        if (!$timezone) {
            $timezone = LocaleLanguage::getTimezone();
        }

        $dt = DateTime::createFromFormat('U', $this->timestamp)->setTimezone(new DateTimeZone($timezone));
        $b = match (true) {
            $dt >= new DateTime('March 20') && $dt < new DateTime('June 20') => 0,
            $dt >= new DateTime('June 20') && $dt < new DateTime('September 22') => 1,
            $dt >= new DateTime('September 22') && $dt < new DateTime('December 21') => 2,
            default => 3,
        };
        $K = floor($dt->format('z') * 100 / (365.24 + $dt->format('L')));

        $result = '';
        for ($index = 0; $index < strlen($format); $index++) {
            $parameter = substr($format, $index, 1);
            if ($parameter == '\\') {
                $result .= substr($format, ++$index, 1);
                continue;
            }
            switch ($parameter) {
                case 'c': case 'd': case 'e': case 'g': case 'h': case 'i': case 'j': case 'm': case 'n': case 'o':
                case 's': case 't': case 'u': case 'w': case 'y': case 'z': case 'B': case 'G': case 'H': case 'I':
                case 'L': case 'N': case 'O': case 'P': case 'T': case 'U': case 'W': case 'Y': case 'Z':
                    $result .= $dt->format($parameter);
                    break;
                case 'a': case 'A': case 'D': case 'l': case 'S':
                    $result .= ExpressionService::getXua('services.date_time_instance.format.' . $parameter . '.' . $dt->format($parameter));
                    break;
                case 'F': case 'M':
                    $result .= ExpressionService::getXua('services.date_time_instance.format.' . $parameter . '.gregorian.' . $dt->format($parameter));
                    break;
                case 'r':
                    $D = ExpressionService::getXua('services.date_time_instance.format.D.' . $dt->format('D'));
                    $d = $dt->format('d');
                    $M = ExpressionService::getXua('services.date_time_instance.format.M.' . $dt->format('M'));
                    $Y = $dt->format('Y');
                    $H = $dt->format('H');
                    $i = $dt->format('i');
                    $s = $dt->format('s');
                    $O = $dt->format('O');
                    $result .= "{$D}, $d $M $Y $H:$i:$s $O";
                    break;
                case 'b':
                    $result .= $b + 1;
                    break;
                case 'C':
                    $result .= floor(($dt->format('Y') + 99) / 100);
                    break;
                case 'f':
                    $result .= ExpressionService::getXua('services.date_time_instance.format.' . $parameter . '.' . $b);
                    break;
                case 'J':
                    // @TODO
//                    $result .= ExpressionService::numberToText($d);
                    $result .= 'J';
                    break;
                case 'k';
                    $result .= 100 - $K;
                    break;
                case 'K':
                    $result .= $K;
                    break;
                case 'p':
                    $result .= ExpressionService::getXua('services.date_time_instance.format.' . $parameter . '.gregorian.' . $dt->format('m'));
                    break;
                case 'q':
                    $result .= ExpressionService::getXua('services.date_time_instance.format.' . $parameter . '.' . ($dt->format('Y') + 3) % 12);
                    break;
                case 'Q':
                    $result .= $dt->format('L') + 364 - $dt->format('z');
                    break;
                case 'v':
                    // @TODO
//                    $result .= ExpressionService::numberToText($y);
                    $result .= 'v';
                    break;
                case 'V':
                    // @TODO
//                    $result .= ExpressionService::numberToText($Y);
                    $result .= 'V';
                    break;
                default:
                    $result .= $parameter;
            }
        }
        return ExpressionService::fixNumbers($result, $lang);
    }

    public function formatJalali(string $format, ?string $timezone = null, ?string $lang = null): string
    {
        if (!$timezone) {
            $timezone = LocaleLanguage::getTimezone();
        }
        [$Y, $m, $d] = self::gregorianToJalali($this->YmdHis['Y'], $this->YmdHis['m'], $this->YmdHis['d']);
        $H = $this->YmdHis['h'];
        $i = $this->YmdHis['i'];
        $s = $this->YmdHis['s'];
        $dt = DateTime::createFromFormat('U', $this->timestamp)->setTimezone(new DateTimeZone($timezone));
        [$O, $P, $w, $N] = explode('-', $dt->format('O-P-w-N'));
        /** @var integer $w */
        $w = (($w + 1) % 7);
        $z = ($m < 7) ? (($m - 1) * 31) + $d - 1 : (($m - 7) * 30) + $d + 185;
        $L = floor(((($Y + 12) % 33) % 4) == 1);
        $b = floor(($m - 1) / 3);
        $K = floor($z * 100 / (365.24 + $L));
        $Q = $L + 364 - $z;
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
                case 'e': case 'g': case 'h': case 'i': case 's': case 'u': case 'B': case 'G': case 'H': case 'I':
                case 'N': case 'O': case 'P': case 'T': case 'U': case 'Z':
                    $result .= date($parameter, $this->timestamp);
                    break;
                // Parameters similar to php `date` function
                case 'a': case 'A': case 'l':
                    $result .= ExpressionService::getXua('services.date_time_instance.format.' . $parameter . '.' . $dt->format($parameter));
                    break;
                case 'c':
                    $result .= "$Y-$m-{$d}T$H:$i:$s$P";
                    break;
                case 'd':
                    $result .= $d;
                    break;
                case 'D':
                    $result .= ExpressionService::getXua('services.date_time_instance.format.' . $parameter . '.' . $w);
                    break;
                case 'F': case 'M': case 'p':
                    $result .= ExpressionService::getXua('services.date_time_instance.format.' . $parameter . '.jalali.' . $m);
                    break;
                case 'j':
                    $result .= +$d;
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
                case 'o':
                    // TODO check (use $N)
                    $result .= ($w > ($z + 3) and $z < 3) ? $Y - 1 : (((3 - $Q) > $w and $Q < 3) ? $Y + 1 : $Y);
                    break;
                case 'r':
                    $D = ExpressionService::getXua('services.date_time_instance.format.D.' . $w);
                    $M = ExpressionService::getXua('services.date_time_instance.format.M.' . $m);
                    $result .= "{$D}، $d $M $Y $H:$i:$s $O";
                    break;
                case 'S':
                    $result .= ExpressionService::getXua('services.date_time_instance.format.' . $parameter . '.' . match (substr($d, -1)) {
                        1 => 'st',
                        2 => 'nd',
                        3 => 'rd',
                        default => 'th',
                    });
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
                    $result .= ExpressionService::getXua('services.date_time_instance.format.' . $parameter . '.' . $b);
                    break;
                case 'J':
                    // @TODO
//                    $result .= ExpressionService::numberToText($d);
                    $result .= 'J';
                    break;
                case 'k';
                    $result .= 100 - $K;
                    break;
                case 'K':
                    $result .= $K;
                    break;
                case 'q':
                    $result .= ExpressionService::getXua('services.date_time_instance.format.' . $parameter . '.' . $Y % 12);
                    break;
                case 'Q':
                    $result .= $Q;
                    break;
                case 'v':
                    // @TODO
//                    $result .= ExpressionService::numberToText($y);
                    $result .= 'v';
                    break;
                case 'V':
                    // @TODO
//                    $result .= ExpressionService::numberToText($Y);
                    $result .= 'V';
                    break;

                // Default
                default:
                    $result .= $parameter;
            }
        }
        return ExpressionService::fixNumbers($result, $lang);
    }

    public function format(string $format, ?string $timezone = null, ?string $lang = null): string
    {
        return match (LocaleLanguage::getCalendar()) {
            LocaleLanguage::CAL_JALALI => $this->formatJalali($format, $timezone, $lang),
            LocaleLanguage::CAL_GREGORIAN => $this->formatGregorian($format, $timezone, $lang),
            default => $this->formatGregorian($format, $timezone, $lang),
        };
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

    // DateTime Modify
    public function modifyGregorian(string $modifier): static
    {
        $this->timestamp = DateTime::createFromFormat('U', $this->timestamp)->modify($modifier)->getTimestamp();
        return $this;
    }

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