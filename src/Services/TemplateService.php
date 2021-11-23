<?php

namespace Xua\Core\Services;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Xua\Core\Eves\Service;

final class TemplateService extends Service
{
    public static Environment $twig;

    private function __construct() {}

    protected static function _init(): void
    {
        self::$twig = new Environment(new FilesystemLoader(ConstantService::get('config', 'services.template.path')), [
            'debug' => EnvironmentService::debugMode(),
            'cache' => ConstantService::get('config', 'services.template.cachePath'),
        ]);
        if (EnvironmentService::debugMode()) {
            self::$twig->addExtension(new DebugExtension());
        }
    }

    public static function render(string $template, array $bind) : string
    {
        return self::$twig->render($template, $bind);
    }
}