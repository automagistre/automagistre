<?php

declare(strict_types=1);

namespace App\EasyAdmin\Form;

use App\Costil;
use App\Doctrine\ORM\Type\Identifier;
use App\Infrastructure\Identifier\IdentifierFormatter;
use function array_filter;
use function array_flip;
use function array_map;
use function assert;
use function current;
use EasyCorp\Bundle\EasyAdminBundle\Router\EasyAdminRouter;
use function is_iterable;
use function iterator_to_array;
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

    public function __construct(IdentifierFormatter $formatter, EasyAdminRouter $router)
    {
        $this->formatter = $formatter;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entityClass = $options['class'];
        $identifierClass = array_flip(Costil::ENTITY)[$entityClass];

        $builder
            ->setDataMapper($this)
            ->resetViewTransformers()
            ->addEventListener(FormEvents::PRE_SET_DATA, static function (PreSetDataEvent $event): void {
                $form = $event->getForm();
                $data = $event->getData() ?? [];

                $options = $form->getConfig()->getOptions();
                $options['compound'] = false;
                $options['choices'] = is_iterable($data) ? $data : [$data];

                unset($options['class']);

                $form->add('autocomplete', ChoiceType::class, $options);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, static function (PreSubmitEvent $event) use ($identifierClass
            ): void {
                $data = $event->getData();
                $form = $event->getForm();
                $options = $form->get('autocomplete')->getConfig()->getOptions();

                $choices = (array) $data['autocomplete'];
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
            [
                'use_uuid' => true,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choice_label' => fn (Identifier $identifier) => $this->formatter->format($identifier),
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
