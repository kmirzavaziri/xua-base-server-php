<?php


namespace Services\XUA;


use Exception;
use XUA\Exceptions\InstantiationException;
use XUA\Exceptions\RouteDefinitionException;
use XUA\Exceptions\RouteException;
use XUA\Service;

final class RouteService extends Service
{
    const TAB_LEN = 2;
    const METHODS = [
        'GET',
        'POST',
        'PUT',
        'DELETE',
    ];

    const FLAG_SLASHES_ALLOWED = 'SA';

    private static array $routes = [];
    private static string $methodRegx = '';
    public static array $routeArgs = [];

    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `RouteService`.');
    }

    /**
     * @throws RouteDefinitionException
     */
    protected static function _init(): void
    {
        self::$methodRegx = implode('|', self::METHODS);

        self::$routes = [];

        $lines = preg_split("/\r\n|\n|\r/", file_get_contents(ConstantService::ROUTE_FILE));
        $level = 0;
        $stack = [&self::$routes];
        foreach ($lines as $lineNo => $line) {
            if ($commentPos = strpos($line, '#')) {
                $line = substr($line, 0, $commentPos);
            }
            if (!$line) {
                continue;
            }
            $lineLevel = (strlen($line)-strlen(ltrim($line))) / self::TAB_LEN;
            if ($lineLevel == $level) {
                $level++;
            } elseif($lineLevel == $level - 1) {
                array_pop($stack);
            } elseif($lineLevel == $level - 2) {
                array_pop($stack);
                array_pop($stack);
                $level--;
            } else {
                throw (new RouteDefinitionException())->setError(
                    "Routes:" . ($lineNo + 1),
                    "Expected " . ($level * self::TAB_LEN) . ", " .
                    (($level - 1) * self::TAB_LEN) . ", or " .
                    (($level - 2) * self::TAB_LEN) . " spaces, got " .
                    ($lineLevel * self::TAB_LEN)
                );
            }

            $lineData = preg_split('/\s+/', ltrim($line));
            $key = array_shift($lineData);
            if (strpos($key, ':') != strlen($key) - 1) {
                throw  (new RouteDefinitionException())->setError(
                    "Routes:" . ($lineNo + 1),
                    "A key must contain only one colon (:) which should be at the end. but got '$key'"
                );
            }
            $key = substr($key, 0, strlen($key) - 1);
            $methods = self::getMethods($lineData);
            $head = &$stack[count($stack) - 1];
            if (strlen($key) > 2 and $key[0] == '{' and str_ends_with($key, '}')) {
                $keyName = substr($key, 1, strlen($key) - 2);
                $key = 'var';
            }
            if (isset($head[$key])) {
                throw (new RouteDefinitionException())->setError(
                    "Routes:" . ($lineNo + 1),
                    $key == 'var'
                        ? "Cannot have two variables under same route"
                        : "Cannot have two routes of a same name '$key' under same route"
                );
            }
            $head[$key] = [];
            $stack[] = &$head[$key];
            if ($lineData) {
                $head[$key][''] = $methods;
            }
            if ($key == 'var') {
                $keyNameData = explode('|', $keyName);
                $keyName = array_shift($keyNameData);
                $head[$key]['']['name'] = $keyName;
                foreach ($keyNameData as $flagName) {
                    $head[$key]['']['flags'][$flagName] = true;
                }
            }
        }
    }

    /**
     * @throws RouteException
     */
    public static function getInterface(string $route, string $method) : string
    {
        $route = trim($route, '/');
        $route = explode('/', $route);
        $route[] = '';
        $route[] = $method;
        $search = self::$routes;
        $lastSARoute = null;
        foreach ($route as $i => $routePart) {
            if (isset($search[$routePart])) {
                $search = $search[$routePart];
            } elseif (isset($search['var'])) {
                $search = $search['var'];
                self::$routeArgs[$search['']['name']] = $routePart;
                if ($search['']['flags'][self::FLAG_SLASHES_ALLOWED] ?? false) {
                    self::$routeArgs[$search['']['name']] = implode('/', array_slice($route, $i, count($route) - $i - 2));
                    $lastSARoute = $search;
                }
            } else {
                break;
            }
        }
        if (is_string($search)) {
            return ConstantService::INTERFACES_NAMESPACE . '\\' . $search;
        } elseif ($lastSARoute and isset($lastSARoute[''][$method])) {
            return ConstantService::INTERFACES_NAMESPACE . '\\' . $lastSARoute[''][$method];
        }
        else {
            throw (new RouteException())->setError($routePart, 'Not found');
        }
    }

    private static function getMethods(array $lineData) : array
    {
        $result = [];
        foreach ($lineData as $lineDatum) {
            $pattern = '/((' . self::$methodRegx . ')\(([^)(]*)\))|([^)(]*)/';
            preg_match($pattern, $lineDatum, $matches);
            $count = count($matches);
            if ($matches[$count - 2]) {
                $result[$matches[$count - 2]] = $matches[$count - 1];
            } else {
                foreach (self::METHODS as $method) {
                    $result[$method] =  $matches[$count - 1];
                }
            }
        }
        return $result;
    }
}