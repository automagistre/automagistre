<?php

declare(strict_types=1);

use App\Yandex\Map\Controller\RedirectController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services
        ->get(RedirectController::class)
        ->tag('controller.service_arguments');
};
