<?php

declare(strict_types=1);

namespace App\Storage\Form\Inventorization;

use App\Form\Type\QuantityType;
use App\Part\Form\PartAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class InventorizationPartType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('partId', PartAutocompleteType::class, [
                'disabled' => $options['part_disabled'],
            ])
            ->add('quantity', QuantityType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => InventorizationPartDto::class,
                'part_disabled' => false,
            ])
        ;
    }
}
