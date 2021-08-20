<?php

declare(strict_types=1);

namespace App\Storage\Form;

use App\Doctrine\Registry;
use App\Storage\Entity\WarehouseView;
use App\Storage\Form\Warehouse\WarehouseTransformer;
use Generator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class WarehouseType extends AbstractType
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(WarehouseTransformer::create($this->registry));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => new CallbackChoiceLoader(function (): iterable {
                $all = $this->registry->findBy(WarehouseView::class, []);

                $callback = static function (WarehouseView $previous = null) use (&$callback, &$all): Generator {
                    foreach ($all as $key => $current) {
                        if (
                            (null === $previous && null === $current->parent)
                            || (null !== $previous && $previous->id->equals($current->parent?->id))
                        ) {
                            yield $current;

                            yield from $callback($current);

                            unset($all[$key]);
                        }
                    }
                };

                foreach ($callback() as $item) {
                    yield $item;
                }
            }),
            'choice_label' => fn (WarehouseView $view) => str_repeat('  - -  ', $view->depth).$view->name,
            'choice_value' => fn (?WarehouseView $view) => null === $view ? null : $view->id->toString(),
            'required' => false,
            'expanded' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'warehouse';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
