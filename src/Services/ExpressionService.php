<?php


namespace Xua\Core\Services;


use Xua\Core\Eves\Service;

final class ExpressionService extends Service
{
    private static array $dict = [];

    private function __construct() {}

    protected static function _init(): void
    {
        $filename = ConstantService::DICTIONARIES_PATH . DIRECTORY_SEPARATOR . self::lang() . '.yml';
        self::$dict = self::dictParse($filename);
    }

    private static function lang()
    {
        return $_SERVER['HTTP_XUA_LANG_SPEC'] ?? ConstantService::DEFAULT_LANG;
    }

    public static function get(string $key, array $bind = []): string
    {
        return preg_replace_callback('/\$([A-Z_a-z]\w*)/', function (array $matches) use($bind) { return $bind[$matches[1]] ?? $matches[1]; }, (self::$dict[$key]) ?? $key);
    }

    public static function implode(array $list): string
    {
        $last = array_pop($list);
        return implode(self::get('comma.separator'), $list) . self::get('last.separator') . $last;
    }

    private static function dictParse(string $filename) : array
    {
        return @yaml_parse_file($filename);
    }
}