<?php

declare(strict_types=1);

namespace App\Income\Form\Supply;

use App\Form\Type\MoneyType;
use App\Form\Type\QuantityType;
use App\Part\Form\PartAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ItemType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('partId', PartAutocompleteType::class, [
                'disabled' => true,
            ])
            ->add('quantity', QuantityType::class)
            ->add('price', MoneyType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => ItemDto::class,
            ])
        ;
    }
}
