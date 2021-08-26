<?php

declare(strict_types=1);

use App\Keycloak\Constants;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes
        ->add(Constants::REDIRECT_ROUTE, '/oauth2/callback')
        ;
    $routes
        ->add('logout', '/logout')
    ;
    $routes
        ->add('login_check', '/login')
        ->methods(['POST'])
    ;

    $routes
        ->import(__DIR__.'/redirects.php')
        ->host('r.automagistre.ru')
    ;

    $routes
        ->import(__DIR__.'/callbacks.php')
        ->host('callback.automagistre.ru')
    ;

    $routes
        ->import(__DIR__.'/../../src/Sms/Controller/CallbackController.php', 'annotation')
        ->host('callback.automagistre.ru')
    ;

    $routes
        ->import(__DIR__.'/../../src/Site/Controller', 'annotation')
    ;

    // MUST BE LAST
    $routes
        ->import('@EasyAdminBundle/Controller/EasyAdminController.php', 'annotation')
    ;
};
