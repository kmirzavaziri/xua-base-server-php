<?php


namespace Services\XUA;


use Exception;
use XUA\Exceptions\InstantiationException;
use XUA\Service;

final class ConstantService extends Service
{
    const ROUTE_FILE = './routes.xrml';
    const INTERFACES_NAMESPACE = 'Interfaces';
    const TEMPLATES_PATH = 'templates';
    const TEMPLATES_CACHE_PATH = false;

    const CONNECTION_DSN = "mysql:host=db;dbname=myfarm";
    const CONNECTION_USERNAME = "root";
    const CONNECTION_PASSWORD = "root";

    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `ConstantService`.');
    }

}