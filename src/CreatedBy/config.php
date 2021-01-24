<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension('doctrine', [
        'dbal' => [
            'types' => [
                'created_by_view' => App\CreatedBy\Doctrine\CreatedByViewType::class,
            ],
        ],
    ]);

    $services = $configurator->services();

    $services->get(App\CreatedBy\EventListener\PostPersistEventListener::class)
        ->tag('doctrine.event_subscriber', ['priority' => 100500])
    ;
};
