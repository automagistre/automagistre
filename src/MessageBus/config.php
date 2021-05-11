<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension('framework', [
        'messenger' => [
            'routing' => [
                App\MessageBus\Async::class => 'async',
            ],
        ],
    ]);

    $services = $configurator->services();

    $services->get(App\MessageBus\EntityRecordedMessageCollectorListener::class)
        ->tag('doctrine.event_subscriber')
    ;
};
