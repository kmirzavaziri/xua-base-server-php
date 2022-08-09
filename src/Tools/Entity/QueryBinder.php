<?php

namespace Xua\Core\Tools\Entity;

abstract class QueryBinder
{
    const SINGLE_QUOTE = "'";

    public static function getQueryAndBind(string $query, array $bind): array
    {
        $arrayBindPositions = [];
        $pos = 0;
        $newBind = [];
        foreach ($bind as $value) {
            $pos = strpos($query, '?', $pos);
            if (is_array($value)) {
                $arrayBindPositions[$pos] = count($value);
                $newBind = array_merge($newBind, $value);
            } elseif (is_a($value, RawSQL::class)) {
                $query = substr($query, 0, $pos) . $value->value . substr($query, $pos + 1);
                $pos = $pos + strlen($value->value) - 1;
            } else {
                $newBind[] = $value;
            }
            $pos++;
        }
        if ($arrayBindPositions) {
            $newQuery = '';
            $start = 0;
            foreach ($arrayBindPositions as $pos => $count) {
                $newQuery .= substr($query, $start, $pos - $start);
                $newQuery .= implode(',', array_fill(0, $count, '?'));
                $start = $pos + 1;
            }
            $newQuery .= substr($query, $start);
            $query = $newQuery;
        }
        return [$query, $newBind];
    }

    public static function bind(string $query, array $bind): string
    {
        return preg_replace_callback('/\?/', function($match) use(&$bind) {
            $value = array_shift($bind);
            if (is_a($value, RawSQL::class)) {
                return $value->value;
            }
            if (is_string($value)) {
                return self::SINGLE_QUOTE . addcslashes($value, self::SINGLE_QUOTE) . self::SINGLE_QUOTE;
            }
            if (is_null($value)) {
                return 'null';
            }
            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }
            return $value;
        }, $query);
    }
}