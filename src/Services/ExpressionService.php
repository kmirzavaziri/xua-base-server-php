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

    public static function getAbsolute(string $key, array $bind, string $path): string
    {
        if (!isset(self::$trees[$path])) {
            self::$trees[$path] = self::parse($path);
        }
        return preg_replace_callback('/\$([A-Z_a-z]\w*)/', function (array $matches) use($bind) { return self::stringify($bind[$matches[1]] ?? $matches[1]); }, self::getKey(self::$trees[$path], $key));
    }

    public static function get(string $key, array $bind = [], string $path = '', ?string $lang = null): string
    {
        if (!$lang or !in_array($lang, LocaleLanguage::LANG_)) {
            $lang = LocaleLanguage::getLanguage();
        }
        $d = DIRECTORY_SEPARATOR;
        $path = ConstantService::get('config', 'services.expression.path') . "$d$path$d$lang.yml";
        return self::getAbsolute($key, $bind, $path);
    }

    public static function getXua(string $key, array $bind = [], ?string $lang = null) {
        if (!$lang or !in_array($lang, LocaleLanguage::LANG_)) {
            $lang = LocaleLanguage::getLanguage();
        }
        $d = DIRECTORY_SEPARATOR;
        $path = dirname(__FILE__) . "$d..$d..${d}private${d}dictionaries$d$lang.yml";
        return self::getAbsolute($key, $bind, $path);
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

    private static function stringify(mixed $value)
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
}