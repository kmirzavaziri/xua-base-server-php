<?php


namespace Services\XUA;


use XUA\Service;

abstract class EnvironmentService extends Service
{
    const ENV_LOCAL = 'local';
    const ENV_DEMO = 'prod';
    const ENV_PROD = 'prod';

    public static function env(): string
    {
        return getenv('ENV_NAME');
    }
}