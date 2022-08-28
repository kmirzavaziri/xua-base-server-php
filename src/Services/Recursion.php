<?php

namespace Xua\Core\Services;

use Xua\Core\Eves\Service;

class Recursion extends Service
{
    public static function get(array $array, array $path): mixed {
        $tmp = $array;
        foreach ($path as $part) {
            if (!isset($tmp[$part])) {
                return null;
            }
            $tmp = $tmp[$part];
        }
        return $tmp;
    }

    public static function set(array &$array, array $path, mixed $value): void {
        $tmp = &$array;
        foreach ($path as $part) {
            if (!isset($tmp[$part]) or !is_array($tmp[$part])) {
                $tmp[$part] = [];
            }
            $tmp = &$tmp[$part];
        }
        $tmp = $value;
    }

    public static function flatten(array $array, string $sep = '.', bool $includeNonLeaves = false, ?callable $getChildren = null): array
    {
        if ($getChildren === null) {
            $getChildren = function (mixed $item): array {
                if (is_array($item)) {
                    return $item;
                }
                return [];
            };
        }
        $result = [];
        foreach ($array as $key => $value) {
            $children = $getChildren($value);
            if (!$children or $includeNonLeaves) {
                $result["$key"] = $value;
            }
            if ($children) {
                $flattenChildren = static::flatten($children, $sep, $includeNonLeaves, $getChildren);
                foreach ($flattenChildren as $childKey => $childValue) {
                    $result["$key$sep$childKey"] = $childValue;
                }
            }
        }
        return $result;
    }
}