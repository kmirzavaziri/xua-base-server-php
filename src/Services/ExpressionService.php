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
        if (!isset(self::$trees[$path])) {
            self::$trees[$path] = self::parse($path);
        }
        $return = self::getKey(self::$trees[$path], $key);
        if ($bind !== null) {
            $return = preg_replace_callback('/\$([A-Z_a-z]\w*)/', function (array $matches) use($bind) { return self::stringify($bind[$matches[1]] ?? $matches[1]); }, $return);
        }
        return is_scalar($return) ? $return : '';
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