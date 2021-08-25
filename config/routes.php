<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes
        ->import(__DIR__.'/../src/ATS/Controller/', 'annotation')
    ;

    $routes
        ->import(__DIR__.'/routes/tenants.php')
        ->prefix('/{tenant}')
    ;
};
