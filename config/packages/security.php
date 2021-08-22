<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security, ContainerConfigurator $configurator) {
    $security->enableAuthenticatorManager(true);

    $security->encoder(App\User\Entity\User::class)
        ->algorithm('auto')
    ;

    $security->provider('entity_by_id')
        ->entity()
        ->class(App\User\Entity\User::class)
        ->property('id')
    ;

    $security->provider('entity_by_username')
        ->entity()
        ->class(App\User\Entity\User::class)
        ->property('username')
    ;

    $security->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false)
    ;

    $security->firewall('callback')
        ->host('callback.automagistre.ru')
        ->provider('entity_by_username')
        ->customAuthenticators([
            App\Sms\Security\CallbackGuard::class,
        ])
        ->stateless(true)
    ;

    $security->firewall('www')
        ->pattern('^/api/www')
        ->stateless(true)
    ;

    $securedFirewall = $security->firewall('secured')
        ->provider('entity_by_id')
        ->customAuthenticators([
            App\Keycloak\Security\KeycloakAuthenticator::class,
        ])
    ;
    $securedFirewall
        ->logout()
        ->path('logout')
    ;

    if ('test' === $configurator->env()) {
        $securedFirewall->httpBasic()
            ->provider('entity_by_username')
        ;
    }

    $security->accessControl()
        ->host('r.automagistre.ru')
        ->roles([AuthenticatedVoter::PUBLIC_ACCESS])
    ;

    $security->accessControl()
        ->path('^/api/www')
        ->roles([AuthenticatedVoter::PUBLIC_ACCESS])
    ;

    $security->accessControl()
        ->path('^/')
        ->roles([AuthenticatedVoter::IS_AUTHENTICATED_REMEMBERED])
    ;

    $security->roleHierarchy('ROLE_SUPER_ADMIN', [
        'ROLE_ADMIN',
    ]);
};
