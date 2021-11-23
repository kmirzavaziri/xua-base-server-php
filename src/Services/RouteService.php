<?php

namespace Xua\Core\Services;

use Xua\Core\Exceptions\XRMLException;
use Xua\Core\Exceptions\RouteException;
use Xua\Core\Eves\Service;

final class RouteService extends Service
{
    const FLAG_SLASHES_ALLOWED = 'SA';

    private static array $routes = [];
    public static array $routeArgs = [];
    public static ?string $method = null;
    public static ?string $route = null;

    private function __construct() {}

    /**
     * @throws XRMLException
     */
    protected static function _init(): void
    {
        self::$routes = (new XRMLParser(file_get_contents(ConstantService::get('config', 'services.route.path'))))->parse();
    }

    /**
     * @throws RouteException
     */
    public static function execute(string $route, string $method) : void
    {
        self::$method = $method;
        if (str_ends_with($route, '/')) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . self::getHttpProtocol() . $_SERVER['HTTP_HOST'] . '/' . self::fixRoute($route));
            header("Connection: close");
            return;
        }
        $route = self::fixRoute($route);
        self::$route = $route;

        if ($method == XRMLParser::METHOD_GET) {
            $isPublicResourcePath = false;
            foreach (ConstantService::get('config', 'paths.public') as $publicPath) {
                if (str_starts_with($route, $publicPath)) {
                    $isPublicResourcePath = true;
                    break;
                }
            }
            if ($isPublicResourcePath) {
                if (!is_file($route)) {
                    throw new RouteException();
                }
                header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
                header('Cache-Control: public');
                header('Content-Transfer-Encoding: Binary');
                header('Content-Length:'.filesize($route));
                header('Content-Disposition: filename="' . basename($route) . '"');
                header('Content-Type:');
                readfile($route);
                return;
            }
        }

        $route = explode('/', $route);
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
                throw (new RouteException())->setError($routePart, 'Not found');
            }
        }
        if (isset($search[XRMLParser::LINE_INTERFACES][$method])) {
            $search[XRMLParser::LINE_INTERFACES][$method]::execute();
        } elseif ($lastSARoute and isset($lastSARoute[XRMLParser::LINE_INTERFACES][$method])) {
            $lastSARoute[XRMLParser::LINE_INTERFACES][$method]::execute();
        }
        else {
            throw (new RouteException())->setError($routePart, 'Not found');
        }
    }

    private static function getHttpProtocol(): string
    {
        /** @noinspection HttpUrlsUsage */
        return str_starts_with($_SERVER['SERVER_PROTOCOL'],'https') ? 'https://' : 'http://';
    }

    private static function fixRoute(string $route): string
    {
        return preg_replace('~/+~', '/', trim($route, '/'));
    }
}