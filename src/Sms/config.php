<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator): void {
    $configurator->parameters()
        ->set('env(SMSAERO_AUTH)', null);

    $configurator->extension('framework', [
        'http_client' => [
            'scoped_clients' => [
                'http_client.smsaero' => [
                    'base_uri' => 'https://gate.smsaero.ru/v2/',
                    'auth_basic' => '%env(SMSAERO_AUTH)%',
                ],
            ],
        ],
    ]);

    $configurator->extension('framework', [
        'messenger' => [
            'routing' => [
                App\Sms\Messages\SendRequestedHandler::class => 'async',
            ],
        ],
    ]);

    $services = $configurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->get(App\Sms\Controller\CallbackController::class)
        ->tag('controller.service_arguments');

    $services
        ->get(App\Sms\Messages\SendRequestedHandler::class)
        ->arg('$httpClient', service('http_client.smsaero'));
};
