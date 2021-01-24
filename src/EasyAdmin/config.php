<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services
        ->get(App\EasyAdmin\ErrorRenderer\EasyAdminErrorRenderer::class)
        ->decorate('twig.error_renderer.html')
        ->args([service(App\EasyAdmin\ErrorRenderer\EasyAdminErrorRenderer::class.'.inner')])
        ->arg('$debug', '%kernel.debug%')
    ;
};
