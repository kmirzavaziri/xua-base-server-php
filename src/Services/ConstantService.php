<?php

namespace Xua\Core\Services;

use Xua\Core\Eves\Service;

final class ConstantService extends Service
{
    private static array $cache = [];

    private function __construct() {}

    public static function get(string $treeName, string $nodePath = '') : mixed
    {
        if (!isset(self::$cache[$treeName])) {
            self::$cache[$treeName] = self::envSub(@json_decode(@file_get_contents("$treeName.json"), true) ?? []);
        }

        $nodeNames = array_filter(explode('.', $nodePath));
        $tmp = self::$cache[$treeName];
        foreach ($nodeNames as $nodeName) {
            if (isset($tmp[$nodeName])) {
                $tmp = $tmp[$nodeName];
            } else {
                $tmp = null;
                break;
            }
        }
        return $tmp;
    }

    private static function envSub(array $tree): array
    {
        foreach ($tree as $key => $node) {
            if (is_string($node) and str_starts_with($node, '$')) {
                $tree[$key] = getenv(substr($node, 1, strlen($node) - 1)) ?? $node;
            } elseif (is_array($node)) {
                $tree[$key] = self::envSub($node);
            }
        }
        return $tree;
    }
}