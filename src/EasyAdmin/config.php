<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->set(App\EasyAdmin\ErrorRenderer\EasyAdminErrorRenderer::class)
        ->decorate('twig.error_renderer.html')
        ->args([ref(App\EasyAdmin\ErrorRenderer\EasyAdminErrorRenderer::class.'.inner')])
        ->arg('$debug', '%kernel.debug%')
    ;
};
