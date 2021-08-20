<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension('framework', [
        'messenger' => [
            'routing' => [
                App\Order\Messages\OrderDealed::class => 'async',
                App\Order\Messages\OrderItemPartCreated::class => 'async',
            ],
        ],
    ]);

    $services = $configurator->services();

    $services->get(App\Order\Number\AddSequenceToSchemaListener::class)
        ->tag('doctrine.event_subscriber')
        ;
};
