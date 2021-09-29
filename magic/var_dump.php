<?php
const XUA_VAR_DUMP_MAX_DEPTH = 4;
const XUA_VAR_DUMP_TAB = '  ';

function xua_var_dump(mixed $value, int $level = 0, $visited = []) : string
{
    if (is_object($value)) {
        if (in_array($value, $visited, true)) {
            return "*RECURSION*";
        } else {
            $visited[] = $value;
        }
    }

    $result = '';
    if (is_bool($value)) {
        $result .= $value ? "bool(true)" : "bool(false)";
    } elseif (is_int($value)) {
        $result .= "int($value)";
    } elseif (is_float($value)) {
        $result .= "float($value)";
    } elseif (is_string($value)) {
        $length = strlen($value);
        $result .= "string($length) \"$value\"";
    } elseif (is_callable($value)) {
        $f = new ReflectionFunction($value);
        $name = $f->getName();
        $params = $f->getParameters();
        $params = array_map(function (ReflectionParameter $param) {
            return $param->getType() . ($param->getType() ? ' ' : '') . $param->getName() . ($param->isDefaultValueAvailable() ? ' = ' . $param->getDefaultValue() : '');
        }, $params);
        $params = implode(', ', $params);
        $type = $f->getReturnType();
        $type =  $type ? " : $type" : '';
        $result .= "$name($params)$type";
    } elseif (is_resource($value)) {
        $result .= "resource()";
    } elseif (is_null($value)) {
        $result .= "NULL";
    } else {
        $isArray = is_array($value);
        $result .= ($isArray ? '[' : get_class($value) . ' {');
        if (is_object($value)) {
            $value = method_exists($value, '__debugInfo') ? $value->__debugInfo() : get_object_vars($value);
        }
        if (XUA_VAR_DUMP_MAX_DEPTH and $level >= XUA_VAR_DUMP_MAX_DEPTH) {
            $result .= ' *MAX_DEPTH_REACHED* ';
        } else {
            $result .= PHP_EOL;
            $items = [];
            foreach ($value as $k => $v) {
                $tmp = (is_numeric($k) ? $k : "'$k'") . " => " . xua_var_dump($v, $level + 1, $visited);
                $tmp = implode(PHP_EOL, array_map(function (string $line) {
                    return XUA_VAR_DUMP_TAB . $line;
                }, explode(PHP_EOL, $tmp)));
                $items[] = $tmp;
            }
            $result .= implode(',' . PHP_EOL, $items) . PHP_EOL;
        }
        $result .= ($isArray ? ']' : '}');
    }

    return $result;
}

function var_dump (mixed $value, mixed ...$values) : void
{
    array_unshift($values, $value);
    foreach ($values as $value) {
        print '<pre>' . xua_var_dump($value) . '</pre>';
    }
}