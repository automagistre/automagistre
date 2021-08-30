<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    if ('test' === $configurator->env()) {
        $services->remove(App\User\View\KeycloakUserFormatter::class);
    } else {
        $services->remove(App\User\View\DummyUserFormatter::class);
    }
};
