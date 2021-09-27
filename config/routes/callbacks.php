<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes
        ->import(__DIR__.'/../../src/ATS/Controller/InteractiveController.php', 'annotation')
    ;

    $routes
        ->import(__DIR__.'/../../src/Sms/Controller/CallbackController.php', 'annotation')
    ;
};
