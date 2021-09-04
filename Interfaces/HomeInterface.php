<?php

namespace Interfaces;

use Services\XUA\TemplateService;
use XUA\InterfaceEve;
use XUA\XUA;

class HomeInterface extends InterfaceEve
{
    public static function execute(): string
    {
        return TemplateService::render('home.twig', ['data' => ['version' => XUA::VERSION]]);
    }
}