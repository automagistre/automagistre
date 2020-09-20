<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension('framework', [
        'messenger' => [
            'routing' => [
                App\Calendar\Event\EntryScheduled::class => 'async',
            ],
        ],
    ]);

    $services = $configurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();
};
