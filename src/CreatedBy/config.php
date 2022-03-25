<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (FrameworkConfig $framework, ContainerConfigurator $configurator): void {
    $framework->cache()
        ->pool('cache.created_by')
        ->adapters('prod' === $configurator->env() ? ['cache.adapter.redis'] : ['cache.adapter.array'])
    ;

    $services = $configurator->services();

    $services->get(App\CreatedBy\EventListener\PostPersistEventListener::class)
        ->tag('doctrine.event_subscriber', ['priority' => 100500])
    ;

    $services->get(App\CreatedBy\View\CreatedByExtension::class)
        ->arg('$cache', service('cache.created_by'))
    ;
};
