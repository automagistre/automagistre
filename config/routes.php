<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes
        ->add(App\Keycloak\Constants::CALLBACK_ROUTE, '/oauth2/callback')
    ;
    $routes
        ->add('logout', '/logout')
    ;
    $routes
        ->add('login_check', '/login')
        ->methods(['POST'])
    ;

    $routes
        ->add('metrics', '/metrics')
        ->controller(Artprima\PrometheusMetricsBundle\Controller\MetricsController::class.'::prometheus')
    ;

    $routes
        ->add('healthcheck', '/healthcheck')
        ->controller(App\Healthcheck\Controller\HealthcheckAction::class)
        ;

    // MUST BE LAST
    $routes
        ->import(__DIR__.'/routes/tenants.php')
        ->prefix('/{tenant}')
    ;
};
