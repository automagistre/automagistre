<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services
        ->set(App\Identifier\IdentifierFormatter::class)
        ->public()
        ->args([
            '$formatters' => tagged_locator('identifier_formatter', defaultIndexMethod: 'support'),
        ])
    ;
};
