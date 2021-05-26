<?php

namespace Interfaces\XUA;

use Services\XUA\TemplateService;
use XUA\InterfaceEve;

class NotFoundInterface extends InterfaceEve
{
    public static function execute(): string
    {
        http_response_code(404);
        return TemplateService::render('errors/404.twig', self::$bind);
    }
}