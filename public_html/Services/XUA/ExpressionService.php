<?php


namespace Services\XUA;


use XUA\Exceptions\InstantiationException;
use XUA\Service;

final class ExpressionService extends Service
{
    private static array $dict = [];

    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `ExpressionService`.');
    }

    protected static function _init(): void
    {
        $filename = ConstantService::DICTIONARIES_PATH . DIRECTORY_SEPARATOR . self::lang() . '.yml';
        self::$dict = self::dictParse($filename);
    }

    private static function lang()
    {
        return $_SERVER['HTTP_XUA_LANG_SPEC'] ?? 'en';
    }

    public static function get(string $key, array $bind = []): string
    {
        return preg_replace_callback('/\$([A-Z_a-z]\w*)/', function (array $matches) use($bind) { return $bind[$matches[1]]; }, self::$dict[$key] ?? $key);
    }

    private static function dictParse(string $filename) : array
    {
        if (($content = file_get_contents($filename)) === false) {
            return [];
        }

        $lines = preg_split("/\r\n|\n|\r/", $content);

        $result = [];
        foreach ($lines as $line) {
            $lineData = explode(':', $line);
            if (count($lineData) != 2) {
                continue;
            }
            $result[$lineData[0]] = $lineData[1];
        }

        return $result;
    }
}