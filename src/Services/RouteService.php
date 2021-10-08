<?php


namespace XUA\Services;


use XUA\Exceptions\InstantiationException;
use XUA\Exceptions\XRMLException;
use XUA\Exceptions\RouteException;
use XUA\Eves\Service;

final class RouteService extends Service
{
    const FLAG_SLASHES_ALLOWED = 'SA';

    private static array $routes = [];
    public static array $routeArgs = [];

    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `RouteService`.');
    }

    /**
     * @throws XRMLException
     */
    protected static function _init(): void
    {
        self::$routes = (new XRMLParser(file_get_contents(ConstantService::ROUTE_FILE)))->parse();
    }

    /**
     * @throws RouteException
     */
    public static function getInterface(string $route, string $method) : string
    {
        $route = trim($route, '/');
        $route = explode('/', $route);
        $route[] = '';
        $search = self::$routes;
        $lastSARoute = null;
        foreach ($route as $i => $routePart) {
            if (isset($search[$routePart])) {
                $search = $search[$routePart];
            } elseif (isset($search[XRMLParser::KEY_KEY_VAR])) {
                $search = $search[XRMLParser::KEY_KEY_VAR][''];
                if (isset($search[XRMLParser::LINE_KEY][XRMLParser::KEY_FLAGS][self::FLAG_SLASHES_ALLOWED])) {
                    self::$routeArgs[$search[XRMLParser::LINE_KEY][XRMLParser::KEY_NAME]] = implode('/', array_slice($route, $i, count($route) - $i - 1));
                    $lastSARoute = $search;
                } else {
                    self::$routeArgs[$search[XRMLParser::LINE_KEY][XRMLParser::KEY_NAME]] = $routePart;
                }
            } else {
                break;
            }
        }
        if (isset($search[XRMLParser::LINE_INTERFACES][$method])) {
            return $search[XRMLParser::LINE_INTERFACES][$method];
        } elseif ($lastSARoute and isset($lastSARoute[XRMLParser::LINE_INTERFACES][$method])) {
            return $lastSARoute[XRMLParser::LINE_INTERFACES][$method];
        }
        else {
            throw (new RouteException())->setError($routePart, 'Not found');
        }
    }
}