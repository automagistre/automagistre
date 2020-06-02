<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes
        ->add('logout', '/logout')
    ;
    $routes
        ->add('login_check', '/login')
        ->methods(['POST'])
    ;

    $routes
        ->import(__DIR__.'/../src/User/Controller/SecurityController.php', 'annotation')
    ;
    $routes
        ->import(__DIR__.'/../src/EasyAdmin/Controller/HomeController.php', 'annotation')
    ;

    $routes
        ->add('json_rpc', '/api/v1')
        ->controller(App\JSONRPC\EndpointAction::class)
        ->methods(['POST'])
    ;

    $routes
        ->import(__DIR__.'/../src/Sms/Controller/CallbackController.php', 'annotation')
    ;

    $routes
        ->import(__DIR__.'/routes/tenant.php')
        ->prefix('/{tenant}')
    ;
};
