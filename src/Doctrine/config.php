<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->get(App\Doctrine\Migrations\EventSubscriber\RecreateViewsOnMigration::class)
        ->arg('$path', '%kernel.project_dir%/views')
        ;
};
