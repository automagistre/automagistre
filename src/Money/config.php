<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension('doctrine', [
        'dbal' => [
            'types' => [
                'money' => App\Money\ORM\MoneyType::class,
            ],
        ],
    ]);
};
