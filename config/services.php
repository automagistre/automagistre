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
        ->bind('string $telegramBotToken', '%env(TELEGRAM_BOT_TOKEN)%')
    ;

    $services->instanceof(Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface::class)
        ->tag('doctrine.event_subscriber')
        ;

    $services->set(Premier\Enum\Symfony\EnumNormalizer::class)
        ->tag('serializer.normalizer')
        ;

    $services
        ->instanceof(App\Identifier\IdentifierFormatterInterface::class)
        ->tag('identifier_formatter')
    ;

    $services
        ->instanceof(Premier\Identifier\Identifier::class)
        ->tag(App\Identifier\DI\RegisterIdentifiersCompilerPass::TAG)
    ;

    $services
        ->load('App\\', dirname(__DIR__).'/src')
        ->exclude(dirname(__DIR__).'/src/**/config.php')
    ;

    $services
        ->get(App\Form\TypeGuesser\EntityModelTypeGuesser::class)
        ->arg('$guesser', service('form.type_guesser.doctrine'))
    ;
};
