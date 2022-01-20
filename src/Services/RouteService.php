<?php

namespace Xua\Core\Services;

use Xua\Core\Exceptions\DieException;
use Xua\Core\Exceptions\XRMLException;
use Xua\Core\Exceptions\RouteException;
use Xua\Core\Eves\Service;

final class RouteService extends Service
{
    const FLAG_SLASHES_ALLOWED = 'SA';

    private static array $routes = [];
    public static array $args = [];
    public static ?string $method = null;
    public static ?string $route = null;
    public static ?array $params;

    private function __construct() {}

    /**
     * @throws XRMLException
     */
    protected static function _init(): void
    {
        self::$routes = (new XRMLParser(file_get_contents(ConstantService::get('config', 'services.route.path'))))->parse();
    }

    /**
     * @throws RouteException|DieException
     */
    public static function execute(string $route, string $method) : void
    {
        self::$method = $method;
        $route = explode('?', $route, 2)[0];
        self::$route = self::fixRoute($route);
        $fixedRouteForRedirect = '/' . self::$route;
        if ($route != $fixedRouteForRedirect) {
            $query = http_build_query($_GET);
            self::redirect301(self::getSiteRoot() . $fixedRouteForRedirect . ($query ? ('?' . $query) : ''));
        }

        $routeParts = explode('/', self::$route);
        if (end($routeParts) != '') {
            $routeParts[] = '';
        }
        $search = self::$routes;
        $lastSARoute = null;
        foreach ($routeParts as $i => $routePart) {
            if (isset($search[XRMLParser::KEY_KEY_VAR][''][XRMLParser::LINE_KEY][XRMLParser::KEY_FLAGS][self::FLAG_SLASHES_ALLOWED])) {
                self::$args[$search[XRMLParser::KEY_KEY_VAR][''][XRMLParser::LINE_KEY][XRMLParser::KEY_NAME]] = implode('/', array_slice($routeParts, $i, count($routeParts) - $i - 1));
                $lastSARoute = $search[XRMLParser::KEY_KEY_VAR][''];
            }
            if (isset($search[$routePart])) {
                $search = $search[$routePart];
            } elseif (isset($search[XRMLParser::KEY_KEY_VAR])) {
                $search = $search[XRMLParser::KEY_KEY_VAR];
                if (!isset($search[''][XRMLParser::LINE_KEY][XRMLParser::KEY_FLAGS][self::FLAG_SLASHES_ALLOWED])) {
                    self::$args[$search[''][XRMLParser::LINE_KEY][XRMLParser::KEY_NAME]] = $routePart;
                }
            } else {
                break;
            }
        }
        if (isset($search[XRMLParser::LINE_INTERFACES][$method])) {
            self::$params = $search[XRMLParser::LINE_INTERFACES][$method][XRMLParser::KEY_INTERFACES_PARAMS];
            $search[XRMLParser::LINE_INTERFACES][$method][XRMLParser::KEY_INTERFACES_INTERFACE]::execute();
        } elseif (isset($lastSARoute[XRMLParser::LINE_INTERFACES][$method])) {
            self::$params = $lastSARoute[XRMLParser::LINE_INTERFACES][$method][XRMLParser::KEY_INTERFACES_PARAMS];
            $lastSARoute[XRMLParser::LINE_INTERFACES][$method][XRMLParser::KEY_INTERFACES_INTERFACE]::execute();
        }
        else {
            throw (new RouteException())->setError($routePart, 'Not found');
        }
    }

    public static function getHttpProtocol(): string
    {
        /** @noinspection HttpUrlsUsage */
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            ? 'https://'
            : 'http://';
    }

    private static function fixRoute(string $route): string
    {
        return preg_replace('~/+~', '/', trim($route, '/'));
    }

    public static function getSiteRoot()
    {
        return isset($_SERVER['HTTP_HOST'])
            ? self::getHttpProtocol() . $_SERVER['HTTP_HOST']
            : ConstantService::get('config', 'site.url');
    }

    public static function redirect301(string $location): void
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $location", true, 301);
        header("Connection: close");
        throw new DieException();
    }

    public static function redirect302(string $location): void
    {
        header("HTTP/1.1 302 Found");
        header("Location: $location", true, 302);
        header("Connection: close");
        throw new DieException();
    }
}