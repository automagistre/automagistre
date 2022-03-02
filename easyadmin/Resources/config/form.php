<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\DependencyInjection\DependencyInjectionExtension;
use function Symfony\Component\DependencyInjection\Loader\Configurator\iterator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminFormType::class)
        ->tag('form.type', ['alias' => 'easyadmin'])
        ->args([
            service(EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager::class),
            [],
            service(EasyCorp\Bundle\EasyAdminBundle\Security\AuthorizationChecker::class),
        ])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminFiltersFormType::class)
        ->tag('form.type', ['alias' => 'easyadmin_filters'])
        ->args([service(EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager::class), []])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminAutocompleteType::class)
        ->tag('form.type', ['alias' => 'easyadmin_autocomplete'])
        ->args([service(EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager::class)])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminBatchFormType::class)
        ->tag('form.type', ['alias' => 'easyadmin_batch'])
        ->args([service(EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager::class)])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminDividerType::class)
        ->tag('form.type', ['alias' => 'easyadmin_divider'])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminSectionType::class)
        ->tag('form.type', ['alias' => 'easyadmin_section'])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminGroupType::class)
        ->tag('form.type', ['alias' => 'easyadmin_group'])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Extension\EasyAdminExtension::class)
        ->tag('form.type_extension', ['alias' => 'form', 'extended_type' => FormType::class, 'extended-type' => FormType::class])
        ->args([service('request_stack')])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\CodeEditorTypeConfigurator::class)
        ->tag('easyadmin.form.type.configurator', ['priority' => 50])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\Configurator\ChoiceFilterTypeConfigurator::class)
        ->tag('easyadmin.form.type.configurator', ['priority' => 0])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\TextareaTypeConfigurator::class)
        ->tag('easyadmin.form.type.configurator', ['priority' => 40])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\AutocompleteTypeConfigurator::class)
        ->tag('easyadmin.form.type.configurator', ['priority' => 30])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\CollectionTypeConfigurator::class)
        ->tag('easyadmin.form.type.configurator', ['priority' => 20])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\CheckboxTypeConfigurator::class)
        ->tag('easyadmin.form.type.configurator', ['priority' => 10])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\TypeConfigurator::class)
        ->tag('easyadmin.form.type.configurator', ['priority' => 0])
        ->args([service(EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager::class)])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\EntityTypeConfigurator::class)
        ->tag('easyadmin.form.type.configurator', ['priority' => -20])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\IvoryCKEditorTypeConfigurator::class)
        ->tag('easyadmin.form.type.configurator', ['priority' => -130])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Type\Configurator\FOSCKEditorTypeConfigurator::class)
        ->tag('easyadmin.form.type.configurator', ['priority' => -130])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\FilterRegistry::class)
        ->public()
        ->args([[], iterator([])])
    ;

    $services->set('easyadmin.filter.extension', DependencyInjectionExtension::class)
        ->args(['', [], iterator([])])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Guesser\DoctrineOrmFilterTypeGuesser::class)
        ->tag('easyadmin.filter.type_guesser')
        ->args([service('doctrine')])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Guesser\MissingDoctrineOrmTypeGuesser::class)
        ->public()
        ->tag('form.type_guesser')
        ->args([service('doctrine')])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\ArrayFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'array'])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\BooleanFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'boolean'])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\ComparisonFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'comparison'])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\DateTimeFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'date'])
        ->args([DateType::class])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\ComparisonFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'dateinterval'])
        ->args([DateIntervalType::class, [], '', ['type' => 'datetime']])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\DateTimeFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'datetime'])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\NumericFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'decimal'])
        ->args([NumberType::class, ['input' => 'string']])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\ChoiceFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'choice'])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\EntityFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'entity'])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\NumericFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'float'])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\NumericFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'integer'])
        ->args([IntegerType::class])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\TextFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'text'])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\TextFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'textarea'])
        ->args([TextareaType::class])
    ;

    $services->set(EasyCorp\Bundle\EasyAdminBundle\Form\Filter\Type\DateTimeFilterType::class)
        ->tag('easyadmin.filter.type', ['alias' => 'time'])
        ->args([TimeType::class])
    ;
};
