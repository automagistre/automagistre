<?php

declare(strict_types=1);

namespace App\EasyAdmin\Form;

use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use function array_filter;
use function array_map;
use function assert;
use function current;
use EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use function is_iterable;
use function is_subclass_of;
use function iterator_to_array;
use LogicException;
use function method_exists;
use function sprintf;
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
        $classMetaData = $this->registry->classMetaData($entityClass);
        $reflectionClass = $classMetaData->getReflectionClass();

        $reflectionType = $reflectionClass->getProperty('id')->getType();

        /** @psalm-suppress RedundantCondition */
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
                    $options['formatter_format']
                );

                unset($options['class'], $options['formatter_format']);

                $form->add('autocomplete', ChoiceType::class, $options);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, static function (PreSubmitEvent $event) use ($identifierClass
            ): void {
                $data = $event->getData();
                $form = $event->getForm();
                $options = $form->get('autocomplete')->getConfig()->getOptions();

                $choices = (array) ($data['autocomplete'] ?? []);
                $choices = array_filter($choices, fn (string $choice) => '' !== trim($choice));
                $options['choices'] = array_map(fn (string $uuid) => $identifierClass::fromString($uuid), $choices);

                $form->add('autocomplete', ChoiceType::class, $options);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr']['data-easyadmin-autocomplete-url'] = $this->router->generate(
            $options['class'],
            'autocomplete',
        );
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
            ])
            ->setRequired('class');
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
    public function mapDataToForms($data, $forms): void
    {
        assert($forms instanceof Traversable);
        $form = current(iterator_to_array($forms));
        $form->setData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function mapFormsToData($forms, &$data): void
    {
        assert($forms instanceof Traversable);
        $form = current(iterator_to_array($forms));
        $data = $form->getData();
    }
}
