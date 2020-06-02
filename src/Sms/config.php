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

    $configurator->extension('doctrine', [
        'orm' => [
            'entity_managers' => [
                'tenant' => [
                    'mappings' => [
                        'sms' => [
                            'type' => 'annotation',
                            'prefix' => 'App\\Sms\\Entity',
                            'dir' => '%kernel.project_dir%/src/Sms/Entity',
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $services = $configurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services
        ->set(\App\Sms\Controller\CallbackController::class)
        ->tag('controller.service_arguments');

    $services
        ->set(App\Sms\Action\Send\SendSmsHandler::class)
        ->tag('command_handler', ['handles' => \App\Sms\Action\Send\SendSmsCommand::class]);

    $services
        ->set(\App\Sms\EventListener\SmsSendRequestedListener::class)
        ->arg('$httpClient', service('http_client.smsaero'))
        ->tag('event_subscriber', ['subscribes_to' => \App\Sms\Event\SmsSendRequested::class]);
};
