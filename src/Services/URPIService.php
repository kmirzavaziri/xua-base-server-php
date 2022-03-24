<?php

namespace Xua\Core\Services;

use Throwable;
use Xua\Core\Eves\Entity;
use Xua\Core\Eves\MethodEve;
use Xua\Core\Eves\Service;
use Xua\Core\Eves\Super;
use Xua\Core\Exceptions\MethodRequestException;
use Xua\Core\Exceptions\URPIException;
use Xua\Core\Interfaces\NotFoundInterface;
use Xua\Core\Services\Dev\Credentials;
use Xua\Core\Supers\Highers\Map;
use Xua\Core\Supers\Highers\StructuredMap;
use Xua\Core\Supers\Strings\Symbol;
use Xua\Core\Tools\Signature\Signature;

class URPIService extends Service
{
    const ERRORS = 'errors';
    const RESPONSE = 'response';

    private static Super $jsonResponseType;
    private static array $jsonResponse;
    /** @var URPIService $service
     * @noinspection PhpDocFieldTypeMismatchInspection
     */
    public static string $service;

    protected static function _init(): void
    {
        self::$service = ConstantService::get('config', 'services.urpi.class') ?? self::class;
        self::$jsonResponseType = new StructuredMap([StructuredMap::structure => [
            self::$service::ERRORS   => new Map([Map::keyType => new Symbol([Symbol::allowEmpty => true ])]),
            self::RESPONSE => new Map([Map::keyType => new Symbol([Symbol::allowEmpty => false])])
        ]]);
        self::$jsonResponse = [
            self::ERRORS => [],
            self::RESPONSE => (object)[]
        ];
    }

    public static function main(): void
    {
        $resourcePath = RouteService::$args['resourcePath'];
        if (!$resourcePath) {
            self::$service::notFound();
            return;
        }

        if (RouteService::$method == XRMLParser::METHOD_POST) {
            try {
                self::publicClass($resourcePath);
            } catch (URPIException $e) {
                self::$jsonResponse[self::ERRORS] = $e->getErrors();
                self::respondJson();
            }
            return;
        }

        if (RouteService::$method == XRMLParser::METHOD_GET) {
            try {
                self::publicResource($resourcePath);
            } catch (URPIException $e) {
                self::$service::notFound();
            }
            return;
        }

        self::$service::notFound();
    }

    private static function publicClass(string $resourcePath): void
    {
        $class = ConstantService::get('config', 'services.urpi.rootNamespace') . "\\" . str_replace('/', "\\", $resourcePath);

        if (class_exists($class)) {
            if (is_a($class, MethodEve::class, true)) {
                self::method($class);
            } elseif (is_a($class, Entity::class, true)) {
                self::entity($class);
            }
        }  else {
            URPIService::$service::publicErrorInit();
            throw new URPIException(ExpressionService::get('services.urpi.error_message.invalid_path'));
        }
    }

    /**
     * @throws Throwable
     * @throws URPIException
     */
    private static function method(string $class): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        if (!$class::isPublic() and !SecurityService::verifyPrivateMethodAccess()) {
            throw new URPIException(ExpressionService::get('services.urpi.error_message.invalid_path'));
        }

        $request = @json_decode($_POST['request'], true);
        if ($request === null) {
            throw new URPIException(ExpressionService::get('services.urpi.error_message.invalid_request'));
        }

        try {
            self::$jsonResponse[self::RESPONSE] = (new $class($request))->toArray();
            /** @noinspection PhpUndefinedMethodInspection */
            self::$jsonResponseType = new StructuredMap([StructuredMap::structure => [
                self::ERRORS   => new Map([Map::keyType => new Symbol([Symbol::allowEmpty => true ])]),
                self::RESPONSE => new StructuredMap([StructuredMap::structure => array_map(function (Signature $signature) { return $signature->declaration; }, $class::responseSignatures())])
            ]]);
        } catch (MethodRequestException $e) {
            self::$jsonResponse[self::ERRORS] = $e->getErrors();
        } catch (Throwable $e) {
            if (Credentials::developer()) {
                throw $e;
            } else {
                self::$jsonResponse[self::ERRORS] = ['' => ExpressionService::get('services.urpi.error_message.internal_server')];
            }
        }

        self::respondJson();
    }

    /**
     * @throws URPIException
     */
    private static function entity(Entity $class): void
    {
        throw new URPIException(ExpressionService::get('services.urpi.error_message.invalid_path'));
    }

    private static function publicResource(string $externalResourcePath): void
    {
        self::$service::publicResourceInit();

        $isPublicResourcePath = false;
        $internalResourcePath = '';
        foreach (ConstantService::get('config', 'paths.public') as $publicPath) {
            $explodedPublicPath = explode(':', $publicPath, 2);
            if (count($explodedPublicPath) == 2) {
                [$internalPublicPath, $externalPublicPath] = $explodedPublicPath;
            } else {
                [$internalPublicPath, $externalPublicPath] = [$publicPath, $publicPath];
            }
            if (str_starts_with($externalResourcePath, $externalPublicPath)) {
                $internalResourcePath = $internalPublicPath . DIRECTORY_SEPARATOR . substr($externalResourcePath, strlen($externalPublicPath));
                $internalResourcePath = realpath($internalResourcePath);
                $internalPublicPath = realpath($internalPublicPath);
                if (str_starts_with($internalResourcePath, $internalPublicPath)) {
                    $isPublicResourcePath = true;
                    break;
                }
            }
        }

        if (!$isPublicResourcePath or !is_file($internalResourcePath)) {
            throw new URPIException(ExpressionService::get('services.urpi.error_message.invalid_path'));
        }

        header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
        $mimeType = self::getMimeType($internalResourcePath);
        if (!(Credentials::developer() and in_array($mimeType, ['text/css', 'application/javascript']))) {
            header('Cache-Control: public, max-age=15552000');
            header_remove('Expires');
            header_remove('Pragma');
        }
        header('Content-Transfer-Encoding: Binary');
        header('Content-Length:' . filesize($internalResourcePath));
        header('Content-Disposition: filename="' . basename($externalResourcePath) . '"');
        header('Content-Type:' . $mimeType);
        readfile($internalResourcePath);
    }

    // Helpers
    public static function setOriginHeaders(): void
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            if (in_array($_SERVER['HTTP_ORIGIN'], ConstantService::get('config', 'services.urpi.allowedOrigins.' . EnvironmentService::env()))) {
                header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 86400');
            }
        }
    }

    public static function setOptionsHeaders(array $methods = [XRMLParser::METHOD_POST]): void
    {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
            header("Access-Control-Allow-Methods: " . implode(', ', [...$methods, XRMLParser::METHOD_OPTIONS]));
        }

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
    }

    private static function respondJson(): void
    {
        header('Content-Type: application/json');
        echo self::$jsonResponseType->marshal(self::$jsonResponse);
    }

    private static function getMimeType(string $route): string
    {
        $map = ['txt' => 'text/plain', 'htm' => 'text/html', 'html' => 'text/html', 'php' => 'text/html', 'css' => 'text/css', 'js' => 'application/javascript', 'json' => 'application/json', 'xml' => 'application/xml', 'swf' => 'application/x-shockwave-flash', 'flv' => 'video/x-flv', 'png' => 'image/png', 'jpe' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'gif' => 'image/gif', 'bmp' => 'image/bmp', 'ico' => 'image/vnd.microsoft.icon', 'tiff' => 'image/tiff', 'tif' => 'image/tiff', 'svg' => 'image/svg+xml', 'svgz' => 'image/svg+xml', 'zip' => 'application/zip', 'rar' => 'application/x-rar-compressed', 'exe' => 'application/x-msdownload', 'msi' => 'application/x-msdownload', 'cab' => 'application/vnd.ms-cab-compressed', 'mp3' => 'audio/mpeg', 'qt' => 'video/quicktime', 'mov' => 'video/quicktime', 'pdf' => 'application/pdf', 'psd' => 'image/vnd.adobe.photoshop', 'ai' => 'application/postscript', 'eps' => 'application/postscript', 'ps' => 'application/postscript', 'doc' => 'application/msword', 'rtf' => 'application/rtf', 'xls' => 'application/vnd.ms-excel', 'ppt' => 'application/vnd.ms-powerpoint', 'docx' => 'application/msword', 'xlsx' => 'application/vnd.ms-excel', 'pptx' => 'application/vnd.ms-powerpoint', 'odt' => 'application/vnd.oasis.opendocument.text', 'ods' => 'application/vnd.oasis.opendocument.spreadsheet'];
        return $map[strtolower(pathinfo($route, PATHINFO_EXTENSION))] ?? 'application/octet-stream';
    }

    protected static function notFound(): void
    {
        NotFoundInterface::execute();
    }

    public static function publicMethodInit(): void {}

    public static function publicErrorInit(): void {}

    public static function publicResourceInit(): void {}
}