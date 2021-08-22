<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes
        ->import(__DIR__.'/redirects.php')
        ->host('r.automagistre.ru')
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
