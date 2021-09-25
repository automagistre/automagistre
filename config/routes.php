<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes
        ->add('metrics', '/metrics')
        ->controller(Artprima\PrometheusMetricsBundle\Controller\MetricsController::class.'::prometheus')
    ;

    // MUST BE LAST
    $routes
        ->import(__DIR__.'/routes/tenants.php')
        ->prefix('/{tenant}')
    ;
};
