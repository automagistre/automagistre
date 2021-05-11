<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::PATHS, [
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);
    $parameters->set(Option::ENABLE_CACHE, true);
    $parameters->set(Option::CACHE_DIR, __DIR__.'/var/rector');
    $parameters->set(Option::SYMFONY_CONTAINER_XML_PATH_PARAMETER, __DIR__.'/var/cache/test/App_KernelTestDebugContainer.xml');
    $parameters->set(Option::SKIP, [
        Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector::class => [
            __DIR__.'/src/*/Entity/*',
            __DIR__.'/src/*/Form/*',
            __DIR__.'/tests/*',
        ],
    ]);

    $services = $containerConfigurator->services();

    $services->set(Rector\Restoration\Rector\Namespace_\CompleteImportForPartialAnnotationRector::class)
        ->call('configure', [
            [
                Rector\Restoration\Rector\Namespace_\CompleteImportForPartialAnnotationRector::USE_IMPORTS_TO_RESTORE => ValueObjectInliner::inline([
                    new Rector\Restoration\ValueObject\CompleteImportForPartialAnnotation('Doctrine\ORM\Mapping', 'ORM'),
                ]),
            ],
        ])
    ;

    $services->set(Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector::class);
};
