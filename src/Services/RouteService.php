<?php

namespace Xua\Core\Services;

use Xua\Core\Exceptions\DieException;
use Xua\Core\Exceptions\XRMLException;
use Xua\Core\Exceptions\RouteException;
use Xua\Core\Eves\Service;

final class RouteService extends Service
{
    const FLAG_SLASHES_ALLOWED = 'SA';

    private static self $mainRouteService;

    private readonly array $routes;
    private array $args = [];
    private ?string $method = null;
    private ?string $route = null;
    private array $params = [];

    protected static function _init(): void
    {
        self::$mainRouteService = new self(ConstantService::get('config', 'services.route.path'), true);
    }

    public static function reInitiate(string $path): void
    {
        self::$mainRouteService = new self($path, false);
    }

    private function __construct(string $path, private readonly bool $allowRedirects = false)
    {
        $this->routes = (new XRMLParser(file_get_contents($path)))->parse();
    }

    // main instance methods
    public static function getArg(string $name)
    {
        return self::$mainRouteService->getArgInstance($name);
    }

    public static function getArgs(): array
    {
        return self::$mainRouteService->getArgsInstance();
    }

    public static function getMethod(): ?string
    {
        return self::$mainRouteService->getMethodInstance();
    }

    public static function getRoute(): ?string
    {
        return self::$mainRouteService->getRouteInstance();
    }

    public static function getParam(string $name)
    {
        return self::$mainRouteService->getParamInstance($name);
    }

    public static function getParams(): array
    {
        return self::$mainRouteService->getParamsInstance();
    }

    public static function execute(string $route, string $method): void
    {
        self::$mainRouteService->executeInstance($route, $method);
    }

    // utils
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

    // instance methods
    private function getArgInstance(string $name)
    {
        return $this->getArgsInstance()[$name];
    }

    private function getArgsInstance(): array
    {
        return $this->args;
    }

    private function getMethodInstance(): ?string
    {
        return $this->method;
    }

    private function getRouteInstance(): ?string
    {
        return $this->route;
    }

    private function getParamInstance(string $name)
    {
        return $this->getParamsInstance()[$name];
    }

    private function getParamsInstance(): array
    {
        return $this->params;
    }

    private function executeInstance(string $route, string $method) : void
    {
        $this->method = $method;
        $route = explode('?', $route, 2)[0];
        $this->route = self::fixRoute($route);
        if ($this->allowRedirects) {
            $fixedRouteForRedirect = '/' . $this->route;
            if ($route != $fixedRouteForRedirect) {
                $query = http_build_query($_GET);
                self::redirect301(self::getSiteRoot() . $fixedRouteForRedirect . ($query ? ('?' . $query) : ''));
            }
        }

        $routeParts = explode('/', $this->route);
        if (end($routeParts) != '') {
            $routeParts[] = '';
        }

        $search = $this->routes;
        $lastSARoute = null;
        foreach ($routeParts as $i => $routePart) {
            if (isset($search[XRMLParser::KEY_KEY_VAR][''][XRMLParser::LINE_KEY][XRMLParser::KEY_FLAGS][self::FLAG_SLASHES_ALLOWED])) {
                $this->args[$search[XRMLParser::KEY_KEY_VAR][''][XRMLParser::LINE_KEY][XRMLParser::KEY_NAME]] = implode('/', array_slice($routeParts, $i, count($routeParts) - $i - 1));
                $lastSARoute = $search[XRMLParser::KEY_KEY_VAR][''];
            }
            if (isset($search[$routePart])) {
                $search = $search[$routePart];
            } elseif (isset($search[XRMLParser::KEY_KEY_VAR])) {
                $search = $search[XRMLParser::KEY_KEY_VAR];
                if (!isset($search[''][XRMLParser::LINE_KEY][XRMLParser::KEY_FLAGS][self::FLAG_SLASHES_ALLOWED])) {
                    $this->args[$search[''][XRMLParser::LINE_KEY][XRMLParser::KEY_NAME]] = $routePart;
                }
            } else {
                break;
            }
        }
        if (isset($search[XRMLParser::LINE_INTERFACES][$method])) {
            $this->params = $search[XRMLParser::LINE_INTERFACES][$method][XRMLParser::KEY_INTERFACES_PARAMS];
            $search[XRMLParser::LINE_INTERFACES][$method][XRMLParser::KEY_INTERFACES_INTERFACE]::execute();
        } elseif (isset($lastSARoute[XRMLParser::LINE_INTERFACES][$method])) {
            $this->params = $lastSARoute[XRMLParser::LINE_INTERFACES][$method][XRMLParser::KEY_INTERFACES_PARAMS];
            $lastSARoute[XRMLParser::LINE_INTERFACES][$method][XRMLParser::KEY_INTERFACES_INTERFACE]::execute();
        }
        else {
            throw (new RouteException())->setError($routePart, 'Not found');
        }
    }
}
