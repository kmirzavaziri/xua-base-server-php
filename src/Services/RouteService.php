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
        $route = self::fixRoute($route);
        if ($route != self::fixRoute($route)) {
            self::redirect301(self::getSiteRoot() . ($route ? '/' . $route : ''));
            return;
        }
        $route = explode('?', $route, 2)[0];
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
                header('Content-Length:' . filesize($route));
                header('Content-Disposition: filename="' . basename($route) . '"');
                header('Content-Type:' . self::getMimeType($route));
                readfile($route);
                return;
            }
        }

        $route = [...explode('/', $route)];
        if (end($route) != '') {
            $route[] = '';
        }
        $search = self::$routes;
        $lastSARoute = null;
        foreach ($route as $i => $routePart) {
            if (isset($search[$routePart])) {
                $search = $search[$routePart];
            } elseif (isset($search[XRMLParser::KEY_KEY_VAR])) {
                $search = $search[XRMLParser::KEY_KEY_VAR];
                if (isset($search[''][XRMLParser::LINE_KEY][XRMLParser::KEY_FLAGS][self::FLAG_SLASHES_ALLOWED])) {
                    self::$routeArgs[$search[''][XRMLParser::LINE_KEY][XRMLParser::KEY_NAME]] = implode('/', array_slice($route, $i, count($route) - $i - 1));
                    $lastSARoute = $search[''];
                } else {
                    self::$routeArgs[$search[''][XRMLParser::LINE_KEY][XRMLParser::KEY_NAME]] = $routePart;
                }
            } else {
                break;
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

    public static function getHttpProtocol(): string
    {
        /** @noinspection HttpUrlsUsage */
        return ($_SERVER['SERVER_PROTOCOL'] and str_starts_with($_SERVER['SERVER_PROTOCOL'], 'https'))
            ? 'https://'
            : 'http://';
    }

    private static function fixRoute(string $route): string
    {
        return preg_replace('~/+~', '/', trim($route, '/'));
    }

    public static function getSiteRoot()
    {
        return (isset($_SERVER['HTTP_HOST']) and isset($_SERVER['SERVER_PROTOCOL']))
            ? self::getHttpProtocol() . $_SERVER['HTTP_HOST']
            : ConstantService::get('config', 'site.url');
    }

    private static function getMimeType(string $route): string
    {
        $map = ['txt' => 'text/plain', 'htm' => 'text/html', 'html' => 'text/html', 'php' => 'text/html', 'css' => 'text/css', 'js' => 'application/javascript', 'json' => 'application/json', 'xml' => 'application/xml', 'swf' => 'application/x-shockwave-flash', 'flv' => 'video/x-flv', 'png' => 'image/png', 'jpe' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'gif' => 'image/gif', 'bmp' => 'image/bmp', 'ico' => 'image/vnd.microsoft.icon', 'tiff' => 'image/tiff', 'tif' => 'image/tiff', 'svg' => 'image/svg+xml', 'svgz' => 'image/svg+xml', 'zip' => 'application/zip', 'rar' => 'application/x-rar-compressed', 'exe' => 'application/x-msdownload', 'msi' => 'application/x-msdownload', 'cab' => 'application/vnd.ms-cab-compressed', 'mp3' => 'audio/mpeg', 'qt' => 'video/quicktime', 'mov' => 'video/quicktime', 'pdf' => 'application/pdf', 'psd' => 'image/vnd.adobe.photoshop', 'ai' => 'application/postscript', 'eps' => 'application/postscript', 'ps' => 'application/postscript', 'doc' => 'application/msword', 'rtf' => 'application/rtf', 'xls' => 'application/vnd.ms-excel', 'ppt' => 'application/vnd.ms-powerpoint', 'docx' => 'application/msword', 'xlsx' => 'application/vnd.ms-excel', 'pptx' => 'application/vnd.ms-powerpoint', 'odt' => 'application/vnd.oasis.opendocument.text', 'ods' => 'application/vnd.oasis.opendocument.spreadsheet'];
        return $map[strtolower(pathinfo($route, PATHINFO_EXTENSION))] ?? 'application/octet-stream';
    }


    public static function redirect301(string $location): void
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $location", true, 301);
        header("Connection: close");
    }

    public static function redirect302(string $location): void
    {
        header("HTTP/1.1 302 Found");
        header("Location: $location", true, 302);
        header("Connection: close");
    }
}