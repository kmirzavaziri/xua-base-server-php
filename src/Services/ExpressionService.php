<?php

namespace Xua\Core\Services;

use Xua\Core\Eves\Service;

final class ExpressionService extends Service
{
    const IMPLODE_MODE_COMMA = 'comma';
    const IMPLODE_MODE_CONJUNCTION = 'conjunction';
    const IMPLODE_MODE_DISJUNCTION = 'disjunction';

    private static array $tree = [];

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
            return implode(self::get('xua.services.expression_service.comma_separator'), $array) . self::get('xua.services.expression_service.' . $implodeMode . '_separator') . $last;
        }
    }

    protected static function _init(): void
    {
        $filename = ConstantService::get('config', 'services.expression.path') . DIRECTORY_SEPARATOR . self::lang() . '.yml';
        self::$tree = self::parse($filename);
    }

    private static function lang()
    {
        return $_SERVER['HTTP_XUA_LANG_SPEC'] ?? LocaleLanguage::getLanguage();
    }

    public static function get(string $key, array $bind = []): string
    {
        return preg_replace_callback('/\$([A-Z_a-z]\w*)/', function (array $matches) use($bind) { return self::stringify($bind[$matches[1]] ?? $matches[1]); }, self::getKey($key));
    }

    private static function parse(string $filename) : array
    {
        return @yaml_parse_file($filename) ?: [];
    }

    private static function getKey(string $key)
    {
        $nodeNames = array_filter(explode('.', $key));
        $tmp = self::$tree;
        foreach ($nodeNames as $nodeName) {
            if (isset($tmp[$nodeName])) {
                $tmp = $tmp[$nodeName];
            } else {
                $tmp = end($nodeNames);
                break;
            }
        }
        return $tmp;
    }

    private static function stringify(mixed $value)
    {
        if (is_string($value)) {
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