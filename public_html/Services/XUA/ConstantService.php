<?php


namespace Services\XUA;


use Exception;
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

    function __construct()
    {
        throw new Exception('Cannot instantiate class `ConstantService`.');
    }

}