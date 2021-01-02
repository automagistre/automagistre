<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator): void {
    $configurator->parameters()
        ->set('env(GOOGLE_CREDENTIALS_FILE)', null);

    $services = $configurator->services();

    $services
        ->get(App\Review\Google\Factory::class)
        ->arg('$googleCredentials', '%env(json:GOOGLE_CREDENTIALS_FILE)%');

    $services
        ->set(Google_Client::class, Google_Client::class)
        ->factory([service(App\Review\Google\Factory::class), 'client']);

    $services
        ->set(Google_Service_MyBusiness::class, Google_Service_MyBusiness::class)
        ->args([
            service('Google_Client'),
        ]);

    $services
        ->get(App\Review\Yandex\Controller\RedirectController::class)
        ->tag('controller.service_arguments');
};
