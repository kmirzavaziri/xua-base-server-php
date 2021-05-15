<?php

namespace Interfaces;

use Services\XUA\Dev\Credentials;
use Services\XUA\RouteService;
use Supers\Basics\Highers\Json;
use Throwable;
use XUA\Entity;
use XUA\Exceptions\MethodRequestException;
use XUA\InterfaceEve;
use XUA\Method;

class UniversalResourcePoolInterface extends InterfaceEve
{
    public static function execute(): string
    {
        header('Content-Type: application/json');

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
                    if ($class::isPublic()) {
                        try {
                            $response['response'] = (new $class($_POST))->toArray();
                        } catch (Throwable $e) {
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
                    $response['errors'] = ['' => 'Not implemented yet'];
                } else {
                    $response['errors'] = ['' => 'Invalid path'];
                }

            } else {
                $response['errors'] = ['' => 'Invalid path'];
            }
        }

        if (Credentials::developer()) {
            return json_encode($response, JSON_PRETTY_PRINT);
        } else {
            return json_encode($response);
        }

    }
}