<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set('cache.easyadmin')
        ->parent('cache.system')
        ->tag('cache.pool')
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager::class)
        ->public()
        ->args([
            '%easyadmin.config%',
            '%kernel.debug%',
            service('property_accessor'),
            service('cache.easyadmin'),
        ])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Search\QueryBuilder::class)
        ->public()
        ->args([service('doctrine')])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Search\Finder::class)
        ->args([
            service(EasyCorp\Bundle\EasyAdminBundle\Search\QueryBuilder::class),
            service(EasyCorp\Bundle\EasyAdminBundle\Search\Paginator::class),
        ])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Search\Autocomplete::class)
        ->public()
        ->args([
            service(EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager::class),
            service(EasyCorp\Bundle\EasyAdminBundle\Search\Finder::class),
            service('property_accessor'),
        ])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Search\Paginator::class)
        ->public()
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter::class)
        ->public()
        ->args([
            service(EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager::class),
            service('router'),
            service('property_accessor'),
            service('request_stack'),
        ])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Twig\EasyAdminTwigExtension::class)
        ->tag('twig.extension')
        ->args([
            service(EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager::class),
            service('property_accessor'),
            service(EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter::class),
            '%kernel.debug%',
            service('security.logout_url_generator'),
            service('translator'),
            service(EasyCorp\Bundle\EasyAdminBundle\Security\AuthorizationChecker::class),
        ])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Security\AuthorizationChecker::class)
        ->public()
        ->args([service('security.authorization_checker')])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\EventListener\ControllerListener::class)
        ->public()
        ->tag('kernel.event_listener', ['event' => 'kernel.controller', 'method' => 'onKernelController'])
        ->args([
            service(EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager::class),
            service('controller_resolver'),
        ])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\EventListener\ExceptionListener::class)
        ->public()
        ->tag('kernel.event_listener', ['event' => 'kernel.exception', 'method' => 'onKernelException', 'priority' => -64])
        ->args([
            service('twig'),
            '%easyadmin.config%',
        ])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\EventListener\RequestPostInitializeListener::class)
        ->public()
        ->tag('kernel.event_listener', ['event' => 'easy_admin.post_initialize', 'method' => 'initializeRequest'])
        ->args([
            service('doctrine'),
            service('request_stack'),
        ])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\DataCollector\EasyAdminDataCollector::class)
        ->tag('data_collector', ['template' => '@EasyAdmin/data_collector/easyadmin.html.twig', 'id' => 'easyadmin'])
        ->args([service(EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager::class)])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Configuration\NormalizerConfigPass::class)
        ->tag('easyadmin.config_pass', ['priority' => 90])
        ->args([
            service('service_container'),
            service(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\FilterRegistry::class),
        ])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Configuration\DesignConfigPass::class)
        ->tag('easyadmin.config_pass', ['priority' => 80])
        ->args(['%kernel.default_locale%'])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Configuration\MenuConfigPass::class)
        ->tag('easyadmin.config_pass', ['priority' => 70])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Configuration\ActionConfigPass::class)
        ->tag('easyadmin.config_pass', ['priority' => 60])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Configuration\MetadataConfigPass::class)
        ->tag('easyadmin.config_pass', ['priority' => 50])
        ->args([service('doctrine')])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Configuration\PropertyConfigPass::class)
        ->tag('easyadmin.config_pass', ['priority' => 40])
        ->args([
            service('form.registry'),
            service(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\FilterRegistry::class),
        ])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Configuration\ViewConfigPass::class)
        ->tag('easyadmin.config_pass', ['priority' => 30])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Configuration\TemplateConfigPass::class)
        ->tag('easyadmin.config_pass', ['priority' => 20])
        ->args([service('twig.loader.filesystem')])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Configuration\DefaultConfigPass::class)
        ->tag('easyadmin.config_pass', ['priority' => 10])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController::class)
        ->public()
        ->tag('container.service_subscriber')
        ->autowire()
    ;
};
