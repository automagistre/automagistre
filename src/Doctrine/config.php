<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->get(App\Doctrine\DropViewSchemaListener::class)
        ->tag('doctrine.event_listener', ['event' => 'postGenerateSchema'])
    ;

    $services->get(App\Doctrine\RecreateViewsOnMigration::class)
        ->arg('$path', '%kernel.project_dir%/views')
        ->tag('doctrine.event_subscriber')
        ;
};
