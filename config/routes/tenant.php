<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes
        ->import(__DIR__.'/../../src/Part/Controller', 'annotation')
    ;

    $routes
        ->import(__DIR__.'/../../src/Part/Controller/PartSellController.php', 'annotation')
        ->prefix('/report')
        ->namePrefix('report_')
    ;

    $routes
        ->import(__DIR__.'/../../src/Order/Controller/ProfitController.php', 'annotation')
        ->prefix('/report')
        ->namePrefix('report_')
    ;

    // MUST BE LAST
    $routes
        ->import('@EasyAdminBundle/Controller/EasyAdminController.php', 'annotation')
    ;
};
