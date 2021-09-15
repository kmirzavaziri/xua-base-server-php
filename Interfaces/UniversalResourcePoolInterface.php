<?php

namespace Interfaces;

use Services\XUA\Dev\Credentials;
use Services\XUA\ExpressionService;
use Services\XUA\RouteService;
use Services\XUA\SecurityService;
use Supers\Basics\Highers\Map;
use Supers\Basics\Highers\StructuredMap;
use Supers\Basics\Strings\Symbol;
use Throwable;
use XUA\Entity;
use XUA\Exceptions\MethodRequestException;
use XUA\InterfaceEve;
use XUA\MethodEve;
use XUA\Tools\Signature\MethodItemSignature;

class UniversalResourcePoolInterface extends InterfaceEve
{
    public static function execute(): string
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // @TODO only allow localhost for $_SERVER['HTTP_ORIGIN']
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: POST, OPTIONS");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            return '';
        }

        header('Content-Type: application/json');

        $mapType = new Map(['keyType' => new Symbol(['allowEmpty' => true])]);
        $response = [
            'errors' => [],
            'response' => (object)[]
        ];
        if (empty(RouteService::$routeArgs['methodOrEntityPath'])) {
            $response['errors'] = ['' => 'Invalid path'];
        } else {
            $class = str_replace('/', "\\", RouteService::$routeArgs['methodOrEntityPath']);
            if (class_exists($class)) {
                if (is_a($class, MethodEve::class, true)) {
                    if ($class::isPublic() or SecurityService::verifyPrivateMethodAccess()) {
                        $request = @json_decode($_POST['request'], true);
                        if ($request !== null) {
                            try {
                                $response['response'] = (new $class($request))->toArray();
                                $responseType = new StructuredMap(['structure' => array_map(function (MethodItemSignature $signature) { return $signature->type; }, $class::responseSignatures())]);
                            } catch (Throwable $e) {
                                if (is_a($e, MethodRequestException::class)) {
                                    $unknownKeys = array_diff(array_keys($e->getErrors()), ['', ...array_keys($class::requestSignatures())]);
                                    if ($unknownKeys) {
                                        $e = new MethodRequestException();
                                        foreach ($unknownKeys as $unknownKey) {
                                            $e->setError($unknownKey, ExpressionService::get('errormessage.unknown.request.item'));
                                        }
                                    }
                                }

                                if (is_a($e, MethodRequestException::class)) {
                                    $response['errors'] = $e->getErrors();
                                } else {
                                    if (Credentials::developer()) {
                                        throw $e;
                                    } else {
                                        $response['errors'] = ['' => 'Internal Server Error'];
                                    }
                                }
                            }
                        } else {
                            $response['errors'] = ['' => ExpressionService::get('errormessage.invalid.request')];
                        }
                    } else {
                        $response['errors'] = ['' => 'Access denied'];
                    }
                } elseif (is_a($class, Entity::class, true)) {
                    $response['errors'] = ['' => ExpressionService::get('errormessage.not.implemented.yet')];
                } else {
                    $response['errors'] = ['' => ExpressionService::get('errormessage.invalid.path')];
                }
            } else {
                $response['errors'] = ['' => ExpressionService::get('errormessage.invalid.path')];
            }
        }

        return (new StructuredMap(['structure' => [
            'errors' => $mapType,
            'response' => $responseType ?? $mapType
        ]]))->marshal($response);

    }
}