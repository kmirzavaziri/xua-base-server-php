<?php

namespace XUA\Interfaces;

use XUA\Services\TemplateService;
use XUA\Eves\InterfaceEve;

class NotFoundInterface extends InterfaceEve
{
    public static function execute(): void
    {
        http_response_code(404);
        echo TemplateService::render('errors/404.twig', []);
    }
}