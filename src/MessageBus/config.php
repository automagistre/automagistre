<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->get(App\MessageBus\EntityRecordedMessageCollectorListener::class)
        ->tag('doctrine.event_subscriber');
};
