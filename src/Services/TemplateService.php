<?php


namespace Xua\Core\Services;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Xua\Core\Exceptions\InstantiationException;
use Xua\Core\Eves\Service;

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

    protected static function _init(): void
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