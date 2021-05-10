<?php

namespace Interfaces;

use Services\XUA\TemplateService;
use XUA\InterfaceEve;

class HomeInterface extends InterfaceEve
{
    public static function execute(): string
    {
        return (new TemplateService())->render('test/index.twig', []);
    }
}