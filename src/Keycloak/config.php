<?php

declare(strict_types=1);

use Keycloak\Admin\KeycloakClient;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set(KeycloakClient::class)
        ->factory(KeycloakClient::class.'::factory')
        ->args([
            [
                'realm' => 'automagistre',
                'grant_type' => 'client_credentials',
                'client_id' => 'admin-cli',
                'client_secret' => '%env(KEYCLOAK_CLI_CLIENT_SECRET)%',
                'baseUri' => 'https://auth.automagistre.ru',
            ],
        ])
        ;
};
