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
use XUA\Exceptions\UrpiException;
use XUA\InterfaceEve;
use XUA\Method;
use XUA\Tools\Signature\MethodItemSignature;

class UniversalResourcePoolInterface extends InterfaceEve
{
    public static function execute(): string
    {
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
                if (is_a($class, Method::class, true)) {
                    if ($class::isPublic() or SecurityService::verifyPrivateMethodAccess()) {
                        try {
                            $response['response'] = (new $class($_POST))->toArray();
                            $responseType = new StructuredMap(['structure' => array_map(function (MethodItemSignature $signature) { return $signature->type; }, $class::responseSignatures())]);
                        } catch (Throwable $e) {
                            if (is_a($e, MethodRequestException::class)) {
                                $unknownKeys = array_diff(array_keys($e->getErrors()), ['', ...array_keys($class::requestSignatures())]);
                                if ($unknownKeys) {
                                    $e = new UrpiException();
                                    foreach ($unknownKeys as $unknownKey) {
                                        $e->setError($unknownKey, 'Unknown request item.');
                                    }
                                }
                            }

                            if (is_a($e, MethodRequestException::class)) {
                                $response['errors'] = $e->getErrors();
                            } else {
                                if (Credentials::developer()) {
                                    throw $e;
                                } else {
                                    $response['errors'] = ['' => 'Internal error'];
                                }
                            }
                        }
                    } else {
                        $response['errors'] = ['' => 'Access denied'];
                    }
                } elseif (is_a($class, Entity::class, true)) {
                    $response['errors'] = ['' => ExpressionService::get('errormessage.not.implemented.yet')];
                } else {
                    $response['errors'] = ['' => 'Invalid path'];
                }

            } else {
                $response['errors'] = ['' => 'Invalid path'];
            }
        }

        return (new StructuredMap(['structure' => [
            'errors' => $mapType,
            'response' => $responseType ?? $mapType
        ]]))->marshal($response);

    }
}