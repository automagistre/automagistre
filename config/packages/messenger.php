<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

return static function (ContainerConfigurator $configurator, FrameworkConfig $framework): void {
    $configurator->parameters()->set('env(NSQ_HOST)', 'nsqd');

    $messenger = $framework->messenger();

    $messenger
        ->transport('async')
        ->dsn('nsq://%env(NSQ_HOST)%?topic=symfony-messenger')
        ->options([
            'snappy' => true,
        ])
        ->retryStrategy()
        ->delay(1000)
        ->multiplier(2)
        ->maxRetries(PHP_INT_MAX)
        ->maxDelay(60000)
        ;

    $bus = $messenger
        ->defaultBus('default')
        ->bus('default')
        ->defaultMiddleware('allow_no_handlers')
    ;

    $bus->middleware()->id('router_context');
    $bus->middleware()->id(App\Tenant\Messenger\TenantMiddleware::class);
    $bus->middleware()->id('dispatch_after_current_bus');
    $bus->middleware()->id(App\MessageBus\EntityEventsMiddleware::class);
    $bus->middleware()->id('doctrine_ping_connection');
    $bus->middleware()->id('doctrine_close_connection');
    $bus->middleware()->id('doctrine_transaction');
};
