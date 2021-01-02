<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\inline_service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator): void {
    $configurator->extension('framework', [
        'messenger' => [
            'routing' => [
                App\Review\Event\ReviewReceived::class => 'async',
            ],
        ],
    ]);

    $configurator->parameters()
        ->set('env(GOOGLE_CREDENTIALS_FILE)', '[]');

    $services = $configurator->services();

    $services
        ->get(App\Review\Yandex\Controller\RedirectController::class)
        ->tag('controller.service_arguments');

    $services
        ->set('review.google_client', Google_Client::class)
        ->lazy()
        ->factory([
            inline_service(App\Review\Google\Factory::class)
                ->autowire()
                ->arg('$googleCredentials', '%env(json:GOOGLE_CREDENTIALS_FILE)%'),
            'client',
        ]);

    $services
        ->get(App\Review\Google\Controller\OAuth2Controller::class)
        ->arg('$googleClient', service('review.google_client'));

    $services
        ->get(App\Review\Command\FetchCommand::class)
        ->arg(
            '$fetcher',
            inline_service(App\Review\Fetch\FilterFetcher::class)
                ->autowire()
                ->arg(
                    '$fetcher',
                    inline_service(App\Review\Fetch\ChainFetcher::class)
                        ->args([
                            inline_service(App\Review\Google\GoogleFetcher::class)
                                ->autowire()
                                ->arg(
                                    '$client',
                                    inline_service(Google_Service_MyBusiness::class)
                                        ->args([
                                            service('review.google_client'),
                                        ]),
                                ),
                            inline_service(App\Review\Yandex\YandexFetcher::class)->autowire(),
                        ]),
                ),
        );
};
