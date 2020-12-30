<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension('doctrine', [
        'dbal' => [
            'types' => [
                'appeal_calculator_work' => App\Appeal\Doctrine\Type\CalculatorWorksType::class,
                'appeal_tire_fitting_work' => App\Appeal\Doctrine\Type\TireFittingWorksType::class,
            ],
        ],
    ]);

    $configurator->extension('framework', [
        'messenger' => [
            'routing' => [
                App\Appeal\Event\AppealCreated::class => 'async',
            ],
        ],
    ]);
};
