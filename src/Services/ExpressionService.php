<?php

namespace Xua\Core\Services;

use Xua\Core\Eves\Service;

final class ExpressionService extends Service
{
    const IMPLODE_MODE_COMMA = 'comma';
    const IMPLODE_MODE_CONJUNCTION = 'conjunction';
    const IMPLODE_MODE_DISJUNCTION = 'disjunction';

    private static array $trees = [];

    private function __construct() {}

    public static function implode(array $array, string $implodeMode = self::IMPLODE_MODE_CONJUNCTION)
    {
        $length = count($array);
        if ($length == 0) {
            return '';
        } elseif ($length == 1) {
            return array_pop($array);
        } else {
            $last = array_pop($array);
            return implode(self::getXua('services.expression_service.comma_separator'), $array) . self::getXua('services.expression_service.' . $implodeMode . '_separator') . $last;
        }
    }

    public static function getAbsolute(string $key, ?array $bind, string $path): string
    {
        $return = self::getAbsoluteTree($key, $bind, $path);
        return is_scalar($return) ? $return : '';
    }

    public static function getAbsoluteTree(string $key, ?array $bind, string $path): string|array
    {
        if (!isset(self::$trees[$path])) {
            self::$trees[$path] = self::parse($path);
        }
        $return = self::getKey(self::$trees[$path], $key);
        if ($bind !== null) {
            $return = preg_replace_callback('/\$([A-Z_a-z]\w*)/', function (array $matches) use($bind) { return self::stringify($bind[$matches[1]] ?? $matches[1]); }, $return);
        }
        return $return;
    }

    public static function get(string $key, ?array $bind = null, string $path = '', ?string $lang = null): string
    {
        if (!$lang or !in_array($lang, LocaleLanguage::LANG_)) {
            $lang = LocaleLanguage::getLanguage();
        }
        $d = DIRECTORY_SEPARATOR;
        $path = ConstantService::get('config', 'services.expression.path') . "$d$path$d$lang.yml";
        return self::getAbsolute($key, $bind, $path);
    }

    public static function getTree(string $key, ?array $bind = null, string $path = '', ?string $lang = null): string|array
    {
        if (!$lang or !in_array($lang, LocaleLanguage::LANG_)) {
            $lang = LocaleLanguage::getLanguage();
        }
        $d = DIRECTORY_SEPARATOR;
        $path = ConstantService::get('config', 'services.expression.path') . "$d$path$d$lang.yml";
        return self::getAbsoluteTree($key, $bind, $path);
    }

    public static function getVendor(string $key, ?array $bind = null, string $path = '', ?string $lang = null): string {
        if (!$lang or !in_array($lang, LocaleLanguage::LANG_)) {
            $lang = LocaleLanguage::getLanguage();
        }
        $d = DIRECTORY_SEPARATOR;
        $path = "vendor$d$path$d$lang.yml";
        return self::getAbsolute($key, $bind, $path);
    }

    public static function getXua(string $key, ?array $bind = null, ?string $lang = null): string {
        return self::getVendor($key, $bind, 'xua/core/private/dictionaries', $lang);
    }

    public static function fixNumbers(string $text, ?string $lang = null): string
    {
        $lang = $lang ?? LocaleLanguage::getLanguage();
        $result = '';
        for ($i = 0; $i < mb_strlen($text); $i++) {
            $t = mb_substr($text, $i, 1);
            $result .= self::NUMERIC_MAP[$lang][$t] ?? $t;
        }
        return $result;
    }

    public static function wordify(int $i, string $lang = null, int $level = 0): string {
        if (!$lang or !in_array($lang, LocaleLanguage::LANG_)) {
            $lang = LocaleLanguage::getLanguage();
        }
        $result = '';
        if ($lang == LocaleLanguage::LANG_EN) {
            // @TODO
            $result = '';
        } elseif ($lang == LocaleLanguage::LANG_FA) {
            if ($i < 0) {
                $result = 'منفی ' . self::wordify(-$i, $lang, $level);
            } elseif ($i == 0) {
                if ($level === 0) {
                    $result = 'صفر';
                } else {
                    $result = '';
                }
            } else {
                $yekan = ['یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'];
                $dahgan = [' بیست ', ' سی ', ' چهل ', ' پنجاه ', ' شصت ', ' هفتاد ', ' هشتاد ', ' نود '];
                $sadgan = [' یکصد ', ' دویست ', ' سیصد ', ' چهارصد ', ' پانصد ', ' ششصد ', ' هفتصد ', ' هشتصد ', ' نهصد '];
                $dah = [' ده ', ' یازده ', ' دوازده ', ' سیزده ', ' چهارده ', ' پانزده ', ' شانزده ', ' هفده ', ' هیجده ', ' نوزده '];
                if ($level > 0) {
                    $result .= ' و ';
                    $level -= 1;
                }
                if ($i < 10) {
                    $result .= $yekan[$i - 1];
                } elseif ($i < 20) {
                    $result .= $dah[$i - 10];
                } elseif ($i < 100) {
                    $result .= $dahgan[floor($i / 10) - 2] . self::wordify($i % 10, $lang, $level + 1);
                } elseif ($i < 1000) {
                    $result .= $sadgan[floor($i / 100) - 1] . self::wordify($i % 100, $lang, $level + 1);
                } elseif ($i < 1000000) {
                    $result .= self::wordify(floor($i / 1_000), $lang, $level) . ' هزار ' . self::wordify($i % 1_000, $lang, $level + 1);
                } elseif ($i < 1000000000) {
                    $result .= self::wordify(floor($i / 1_000_000), $lang, $level) . ' میلیون ' . self::wordify($i % 1_000_000, $lang, $level + 1);
                } elseif ($i < 1000000000000) {
                    $result .= self::wordify(floor($i / 1_000_000_000), $lang, $level) . ' میلیارد ' . self::wordify($i % 1_000_000_000, $lang, $level + 1);
                } elseif ($i < 1000000000000000) {
                    $result .= self::wordify(floor($i / 1_000_000_000_000), $lang, $level) . ' تریلیارد ' . self::wordify($i % 1_000_000_000_000, $lang, $level + 1);
                }
            }
        }

        return $result;
    }

    public static function ith(int $i, string $lang = null, $ucFirst = false): string {
        if (!$lang or !in_array($lang, LocaleLanguage::LANG_)) {
            $lang = LocaleLanguage::getLanguage();
        }
        $result = '';
        if ($lang == LocaleLanguage::LANG_EN) {
            $ordinals = [
                'zeroth', 'first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth', 'tenth',
                'eleventh', 'twelfth', 'thirteenth', 'fourteenth', 'fifteenth', 'sixteenth', 'seventeenth', 'eighteenth', 'nineteenth'
            ];
            $tens = ['twent', 'thirt', 'fourt', 'fift', 'sixt', 'sevent', 'eight', 'ninet'];
            $hundreds = array_map(
                function ($item) { return $item . '-hundred'; },
                ['one', 'two', 'three', 'four', 'five',  'six', 'seven', 'eight', 'nine', 'ten'],
            );
            $stringI = '' . $i;
            if ($i < 20) {
                $result = $ordinals[$i];
            } elseif ($i < 100) {
                if ($i % 10) {
                    $result = $tens[$stringI[0] - 2] . 'y-' . $ordinals[$i % 10];
                } else {
                    $result = $tens[$stringI[0] - 2] . 'ieth';
                }
            } elseif ($i < 1000) {
                $left = $stringI[0];
                $result = $hundreds[$left - 1] . ' ' . self::ith($i - ($left * pow(10, strlen($stringI[0]) - 1)), $lang, false);
            }
            if ($ucFirst) {
                $result = ucfirst($result[0]);
            }
        } elseif ($lang == LocaleLanguage::LANG_FA) {
            if ($i == 1) {
                $result = 'اول';
            } else if ($i == 3) {
                // @TODO for others like 23rd
                $result = 'سوم';
            } else {
                $result = self::wordify($i, $lang) . 'م';
            }
        }
        return $result;
    }

    private static function parse(string $filename) : array
    {
        return @yaml_parse_file($filename) ?: [];
    }

    private static function getKey(array $root, string $key)
    {
        $nodeNames = array_filter(explode('.', $key));
        foreach ($nodeNames as $nodeName) {
            if (isset($root[$nodeName])) {
                $root = $root[$nodeName];
            } else {
                $root = end($nodeNames);
                break;
            }
        }
        return $root;
    }

    private static function stringify(mixed $value): string
    {
        if (is_scalar($value)) {
            return $value;
        } elseif (is_null($value)) {
            return 'NULL';
        } elseif (is_array($value)) {
            return self::implode($value);
        } else {
            return '';
        }
    }

    const NUMERIC_MAP = [
        LocaleLanguage::LANG_FA => [
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
        LocaleLanguage::LANG_EN => [
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
}