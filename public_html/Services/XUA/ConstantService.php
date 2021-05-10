<?php


namespace Services\XUA;


use Exception;
use XUA\Service;

class ConstantService extends Service
{
    const ROUTE_FILE = './routes.xrml';
    const INTERFACES_NAMESPACE = 'Interfaces';
    const TEMPLATES_PATH = 'templates';
    const TEMPLATES_CACHE_PATH = false;


    function __construct()
    {
        throw new Exception('Cannot instantiate class `ConstantService`.');
    }

}