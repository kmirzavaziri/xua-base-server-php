<?php

namespace Xua\Core\Interfaces;

use Xua\Core\Services\TemplateService;
use Xua\Core\Eves\InterfaceEve;

class NotFoundInterface extends InterfaceEve
{
    public static function execute(): void
    {
        http_response_code(404);
        echo TemplateService::render('error/404.twig', []);
    }
}