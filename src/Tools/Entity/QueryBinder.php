<?php


namespace Xua\Core\Tools\Entity;

abstract class QueryBinder
{
    public static function getQueryAndBind(string $query, array $bind): array
    {
        $arrayBindPositions = [];
        $pos = 0;
        $newBind = [];
        foreach ($bind as $value) {
            $pos = strpos($query, '?', $pos);
            if (is_array($value)) {
                $arrayBindPositions[$pos] = count($value);
                $newBind   = array_merge($newBind, $value);
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
            return is_string($value) ? "'$value'" : $value;
        }, $query);
    }
}