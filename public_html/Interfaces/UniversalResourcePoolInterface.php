<?php

namespace Interfaces;

use Services\XUA\Dev\Credentials;
use Services\XUA\RouteService;
use Services\XUA\TemplateService;
use Supers\Basics\Highers\Json;
use XUA\InterfaceEve;

class UniversalResourcePoolInterface extends InterfaceEve
{
    public static function execute(): string
    {
        header('Content-Type: application/json');

        $response = [
            'error' => '',
            'errorPath' => '',
            'response' => (object)[]
        ];
        $request = file_get_contents("php://input");
        if (empty(RouteService::$routeArgs['methodOrEntityPath'])) {
            $response['error'] = 'Invalid path';
        } elseif (!(new Json([]))->accepts($request, $message)) {
            $response['error'] = 'Invalid json input';
        } else {
            // @TODO call method or entity
            $response['response'] = $request;
            $response['name'] = RouteService::$routeArgs['methodOrEntityPath'];
        }

        if (Credentials::developer()) {
            return json_encode($response, JSON_PRETTY_PRINT);
        } else {
            return json_encode($response);
        }

    }
}