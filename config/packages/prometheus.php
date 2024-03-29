<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\ArtprimaPrometheusMetricsConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (ArtprimaPrometheusMetricsConfig $prometheus, ContainerConfigurator $configurator): void {
    $prometheus->namespace('automagistre')
        ->disableDefaultMetrics(false)
        ->enableConsoleMetrics(false)
    ;

    if ('test' === $configurator->env()) {
        $prometheus->type('in_memory');
    } else {
        $prometheus->type('redis')
            ->redis()
            ->host(env('REDIS_HOST'))
            ->port(env('int:REDIS_PORT'))
            ->database(1)
        ;
    }

    $prometheus->ignoredRoutes([
        '_profiler',
        '_wdt',
        'metrics',
    ]);
};
