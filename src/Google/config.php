<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension('framework', [
        'messenger' => [
            'routing' => [
                App\Google\Messages\ReviewReceived::class => 'async',
            ],
        ],
    ]);

    $configurator->parameters()
        ->set('env(GOOGLE_CREDENTIALS_FILE)', null);

    $services = $configurator->services();

    $services
        ->get(App\Google\Factory::class)
        ->arg('$googleCredentials', '%env(json:GOOGLE_CREDENTIALS_FILE)%');

    $services
        ->set(Google_Client::class, Google_Client::class)
        ->factory([service(App\Google\Factory::class), 'client']);

    $services
        ->set(Google_Service_MyBusiness::class, Google_Service_MyBusiness::class)
        ->args([
            service('Google_Client'),
        ]);
};
