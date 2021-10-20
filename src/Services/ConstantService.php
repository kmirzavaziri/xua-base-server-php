<?php


namespace Xua\Core\Services;


use Xua\Core\Exceptions\InstantiationException;
use Xua\Core\Eves\Service;
use Xua\Core\Tools\Entity\Database;

final class ConstantService extends Service
{
    const URL = 'http://urpi.mynewfarm.net';

    const PUBLIC_PATHS = ['public', 'favicon.ico'];
    const URPI_ROOT_NAMESPACE = 'Myfarm';

    const ENTITIES_DIR = 'src/Entities';

    const ROUTE_FILE = 'routes.xrml';

    const TEMPLATES_PATH = 'private/templates';
    const TEMPLATES_CACHE_PATH = false;

    const DICTIONARIES_PATH = 'private/dictionaries';

    const STORAGE_PATH = 'public/storage';

    const DEFAULT_LANG = 'fa';

    // @TODO use this
    const VERIFICATION_CODE_LENGTH = 6;
    // @TODO use this
    const VERIFICATION_CODE_EXPIRATION_TIME = DateTimeInstance::MINUTE;

    const DB_TRANSACTION_ISOLATION_LEVEL = Database::TRANSACTION_ISOLATION_LEVEL_SERIALIZABLE;


    private static array $cache = [];

    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `ConstantService`.');
    }

    public static function get(string $treeName, string $nodePath = '') : mixed
    {
        if (!isset(self::$cache[$treeName])) {
            self::$cache[$treeName] = self::getTree($treeName);
        }

        $nodeNames = array_filter(explode('/', $nodePath));
        $tmp = self::$cache[$treeName];
        foreach ($nodeNames as $nodeName) {
            if ($tmp[$nodeName] ?? $tmp[strtoupper($nodeName)] ?? false) {
                $tmp = $tmp[$nodeName] ?? $tmp[strtoupper($nodeName)];
            } else {
                return null;
            }
        }

        return $tmp;
    }

    private static function getTree(string $treeName): array
    {
        if ($content = @file_get_contents("$treeName.json")) {
            $tree = @json_decode($content, true) ?? [];
        } else {
            $variables = getenv();
            $path = strtoupper(str_replace('/', '_', $treeName)) . '_';
            $pathLen = strlen($path);
            $tree = [];
            foreach ($variables as $key => $value) {
                if (str_starts_with($key, $path)) {
                    $relativeKey = substr($key, $pathLen, strlen($key) - $pathLen);
                    $relativeKey = strtolower($relativeKey);
                    $explodedRelativeKey = explode('_', $relativeKey);
                    $tmp = &$tree;
                    foreach ($explodedRelativeKey as $keyPart) {
                        if (!isset($tmp[$keyPart])) {
                            $tmp[$keyPart] = [];
                        }
                        $tmp = &$tmp[$keyPart];
                    }
                    $tmp = $value;
                }
            }
        }
        return $tree;
    }

    public static function url(): string
    {
        switch (EnvironmentService::env()) {
            case EnvironmentService::ENV_LOCAL:
                return 'http://localhost';
            case EnvironmentService::ENV_DEMO:
                return 'http://urpi.mynewfarm.net';
            case EnvironmentService::ENV_PROD:
                return 'http://demo-urpi.mynewfarm.net';
            default:
                return '';
        }
    }
}