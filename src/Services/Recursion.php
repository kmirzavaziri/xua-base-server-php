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
}