<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set(Keycloak\Admin\KeycloakClient::class)
        ->factory(Keycloak\Admin\KeycloakClient::class.'::factory')
        ->args([
            [
                'realm' => 'automagistre',
                'grant_type' => 'client_credentials',
                'client_id' => 'admin-cli',
                'client_secret' => '%env(KEYCLOAK_CLI_CLIENT_SECRET)%',
                'baseUri' => 'https://sso.automagistre.ru',
            ],
        ])
    ;

    $services->set(Stevenmaguire\OAuth2\Client\Provider\Keycloak::class)
        ->lazy(true)
        ->arg('$options', [
            'authServerUrl' => 'https://sso.automagistre.ru/auth',
            'realm' => 'automagistre',
            'clientId' => 'crm-oauth',
            'clientSecret' => '%env(KEYCLOAK_CRM_OAUTH_CLIENT_SECRET)%',
        ])
    ;

    if ('test' === $configurator->env()) {
        $services->remove(App\Keycloak\View\KeycloakUserFormatter::class);
    } else {
        $services->remove(App\Keycloak\View\DummyUserFormatter::class);
    }
};
