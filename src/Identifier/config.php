<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

return static function (FrameworkConfig $framework, ContainerConfigurator $configurator): void {
    $framework->cache()
        ->pool('cache.identifier')
        ->adapters('prod' === $configurator->env() ? ['cache.adapter.redis'] : ['cache.adapter.array'])
    ;

    $services = $configurator->services();

    $services
        ->set(App\Identifier\IdentifierFormatter::class)
        ->public()
        ->arg('$formatters', tagged_locator('identifier_formatter', defaultIndexMethod: 'support'))
        ->arg('$cache', service('cache.identifier'))
    ;
};
