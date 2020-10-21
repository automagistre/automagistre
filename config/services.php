<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('bool $debug', '%kernel.debug%')
        ->bind('string $telegramBotToken', '%env(TELEGRAM_BOT_TOKEN)%');

    $services
        ->instanceof(Doctrine\Common\EventSubscriber::class)
        ->tag('doctrine.event_subscriber');

    $services
        ->instanceof(App\Shared\Identifier\IdentifierFormatterInterface::class)
        ->tag('identifier_formatter');

    $services
        ->load('App\\', dirname(__DIR__).'/src')
        ->exclude(dirname(__DIR__).'/src/**/config.php');

    $services
        ->get(App\Form\TypeGuesser\EntityModelTypeGuesser::class)
        ->arg('$guesser', service('form.type_guesser.doctrine'));
};
