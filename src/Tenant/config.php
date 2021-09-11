<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine, ContainerConfigurator $configurator): void {
    $doctrine->orm()
        ->entityManager('default')
        ->filter('tenant')
        ->class(App\Tenant\Doctrine\ORM\Query\TenantFilter::class)
        ->enabled(true)
    ;

    $doctrine->orm()
        ->entityManager('default')
        ->filter('tenant_group')
        ->class(App\Tenant\Doctrine\ORM\Query\GroupFilter::class)
        ->enabled(true)
    ;

    $services = $configurator->services();
};
