<?php


namespace Services\XUA;


use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use XUA\Exceptions\InstantiationException;
use XUA\Service;

final class TemplateService extends Service
{
    private static Environment $twig;

    /**
     * @throws InstantiationException
     */
    function __construct()
    {
        throw new InstantiationException('Cannot instantiate class `TemplateService`.');
    }

    protected static function _init()
    {
        self::$twig = new Environment(new FilesystemLoader(ConstantService::TEMPLATES_PATH), [
            'cache' => ConstantService::TEMPLATES_CACHE_PATH,
        ]);
    }

    public static function render(string $template, array $bind) : string
    {
        return self::$twig->render($template, $bind);
    }
}