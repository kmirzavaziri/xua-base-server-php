<?php

namespace XUA\Interfaces;

use XUA\Services\TemplateService;
use XUA\Eves\InterfaceEve;

class NotFoundInterface extends InterfaceEve
{
    public static function execute(): string
    {
        http_response_code(404);
        return TemplateService::render('errors/404.twig', []);
    }
}