<?php

declare(strict_types=1);

namespace App\EasyAdmin\Form;

use App\Doctrine\Registry;
use App\Identifier\IdentifierFormatter;
use EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use LogicException;
use Premier\Identifier\Identifier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Traversable;
use function array_filter;
use function array_map;
use function assert;
use function current;
use function is_iterable;
use function is_subclass_of;
use function iterator_to_array;
use function method_exists;
use function sprintf;
use function trim;

final class AutocompleteType extends AbstractType implements DataMapperInterface
{
    private IdentifierFormatter $formatter;

    private EasyAdminRouter $router;

    private Registry $registry;

    public function __construct(IdentifierFormatter $formatter, EasyAdminRouter $router, Registry $registry)
    {
        $this->formatter = $formatter;
        $this->router = $router;
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entityClass = $options['class'];
        $classMetaData = $this->registry->manager($entityClass)->getClassMetadata($entityClass);
        $reflectionClass = $classMetaData->getReflectionClass();

        $reflectionType = $reflectionClass->getProperty('id')->getType();

        assert(null !== $reflectionType && method_exists($reflectionType, 'getName'));
        $identifierClass = $reflectionType->getName();

        if (!is_subclass_of($identifierClass, Identifier::class)) {
            throw new LogicException(sprintf('Can\'t find identifier class for %s', $entityClass));
        }

        $builder
            ->setDataMapper($this)
            ->resetViewTransformers()
            ->addEventListener(FormEvents::PRE_SET_DATA, function (PreSetDataEvent $event): void {
                $form = $event->getForm();
                $data = $event->getData() ?? [];

                $options = $form->getConfig()->getOptions();
                $options['compound'] = false;
                $options['choices'] = is_iterable($data) ? $data : [$data];
                $options['choice_label'] = fn (Identifier $identifier): string => $this->formatter->format(
                    $identifier,
                    $options['formatter_format'],
                );

                unset(
                    $options['class'],
                    $options['widget'],
                    $options['formatter_format'],
                    $options['autocomplete_parameters'],
                );

                $form->add('autocomplete', ChoiceType::class, $options);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, static function (PreSubmitEvent $event) use (
                $identifierClass
            ): void {
                $data = $event->getData();
                $form = $event->getForm();
                $options = $form->get('autocomplete')->getConfig()->getOptions();

                $choices = (array) ($data['autocomplete'] ?? []);
                $choices = array_filter($choices, fn (string $choice) => '' !== trim($choice));
                $options['choices'] = array_map(fn (string $uuid) => $identifierClass::from($uuid), $choices);

                $form->add('autocomplete', ChoiceType::class, $options);
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr']['data-easyadmin-autocomplete-url'] = $this->router->generate(
            $options['class'],
            'autocomplete',
            $options['autocomplete_parameters'],
        );
        $view->vars['with_widget'] = true === $options['disabled'] ? false : ($options['widget'] ?? true);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'formatter_format' => 'autocomplete',
                'choice_value' => fn (?Identifier $identifier) => null === $identifier ? null : $identifier->toString(),
                'autocomplete_parameters' => [],
            ])
            ->setAllowedTypes('autocomplete_parameters', 'array')
            ->setRequired('class')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'autocomplete';
    }

    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($viewData, Traversable $forms): void
    {
        $form = current(iterator_to_array($forms));
        assert($form instanceof FormInterface);
        $form->setData($viewData);
    }

    /**
     * {@inheritdoc}
     */
    public function mapFormsToData(Traversable $forms, &$viewData): void
    {
        $form = current(iterator_to_array($forms));
        assert($form instanceof FormInterface);
        $viewData = $form->getData();
    }
}
