<?php

declare(strict_types=1);

use App\Tenant\Tenant;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services
        ->set(Tenant::class)
        ->factory([Tenant::class, 'fromEnv'])
    ;
};
