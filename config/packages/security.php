<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security, ContainerConfigurator $configurator) {
    $security->enableAuthenticatorManager(true);
    $security->accessDecisionManager()->strategy('unanimous');

    $security->encoder(Symfony\Component\Security\Core\User\InMemoryUser::class)
        ->algorithm('auto')
    ;

    $security->provider('keycloak')
        ->id(App\Keycloak\Security\KeycloakUserProvider::class)
    ;

    $inMemory = $security->provider('in_memory')->memory();
    $inMemory->user(App\Sms\Constants::SMSAERO_USER_ID);

    if ('test' === $configurator->env()) {
        $inMemory
            ->user('1ea9478c-eca4-6f96-a221-3ab8c77b35e5')
            ->password('$2y$13$RO8v5ocI.PAoWqJDsfs0T.qbCemJhO/U3KgB672Y7CxDszFj3GCtK')
        ;
    }

    $security->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false)
    ;

    $security->firewall('callback')
        ->host('callback.automagistre.ru')
        ->provider('in_memory')
        ->customAuthenticators([
            App\Sms\Security\CallbackGuard::class,
        ])
        ->stateless(true)
    ;

    $security->firewall('www')
        ->pattern('^/[a-z0-9]+/api/www')
        ->stateless(true)
    ;

    $securedFirewall = $security->firewall('secured')
        ->provider('keycloak')
        ->customAuthenticators([
            App\Keycloak\Security\KeycloakAuthenticator::class,
        ])
        ->entryPoint(App\Keycloak\Security\KeycloakEntryPoint::class)
    ;
    $securedFirewall
        ->logout()
        ->path('logout')
    ;

    if ('test' === $configurator->env()) {
        $securedFirewall->httpBasic()
            ->provider('in_memory')
        ;
    }

    $security->accessControl()
        ->host('r.automagistre.ru')
        ->roles([AuthenticatedVoter::PUBLIC_ACCESS])
    ;

    $security->accessControl()
        ->path('^/[a-z0-9]+/api/www')
        ->roles([AuthenticatedVoter::PUBLIC_ACCESS])
    ;

    $security->accessControl()
        ->host('crm.automagistre.(ru|local)')
        ->path('^/')
        ->roles([AuthenticatedVoter::IS_AUTHENTICATED_REMEMBERED, App\Tenant\Security\TenantVoter::TENANT_ACCESS])
    ;

    $security->roleHierarchy('ROLE_SUPER_ADMIN', [
        'ROLE_ADMIN',
    ]);
};
