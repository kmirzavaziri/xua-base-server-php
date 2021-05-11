<?php

namespace Interfaces;

use Services\XUA\TemplateService;
use XUA\InterfaceEve;
use XUA\XUA;

class HomeInterface extends InterfaceEve
{
    public static function execute(): string
    {
        self::$bind['data'] = ['version' => XUA::VERSION];
        return TemplateService::render('home.twig', self::$bind);
    }
}