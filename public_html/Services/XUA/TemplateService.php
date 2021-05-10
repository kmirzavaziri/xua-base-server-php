<?php


namespace Services\XUA;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use XUA\Service;

class TemplateService extends Service
{
    private static Environment $twig;

    protected static function _init()
    {
        self::$twig = new Environment(new FilesystemLoader(ConstantService::TEMPLATES_PATH), [
            'cache' => ConstantService::TEMPLATES_CACHE_PATH,
        ]);
    }

    public function render(string $template, array $bind) : string
    {
        return self::$twig->render($template, $bind);
    }
}