<?php


namespace Services\XUA;


use Exception;
use XUA\Exceptions\InstantiationException;
use XUA\Service;

final class ConstantService extends Service
{
    const INTERFACES_NAMESPACE = 'Interfaces';
    const ENTITIES_NAMESPACE = 'Entities';

    const ROUTE_FILE = './routes.xrml';

    const TEMPLATES_PATH = 'templates';
    const TEMPLATES_CACHE_PATH = false;

    const DICTIONARIES_PATH = 'private/dictionaries';

    const DEFAULT_LANG = 'fa';

    private static array $cache = [];

    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `ConstantService`.');
    }

    public static function get(string $filePath, string $nodePath = '') : mixed
    {
        if (!isset(self::$cache[$filePath])) {
            if (!($content = @file_get_contents($filePath))) {
                self::$cache[$filePath] = [];
            } else {
                $data = @json_decode($content);
                if ($data !== null) {
                    self::$cache[$filePath] = $data;
                } else {
                    self::$cache[$filePath] = [];
                }
            }
        }

        $nodeNames = explode('/', $nodePath);
        $tmp = self::$cache[$filePath];
        foreach ($nodeNames as $nodeName) {
            if ($tmp[$nodeName] ?? false) {
                $tmp = $tmp[$nodeName];
            } else {
                return null;
            }
        }

        return $tmp;
    }
}