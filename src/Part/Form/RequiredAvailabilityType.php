<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\Form\Type\QuantityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RequiredAvailabilityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('partId', PartAutocompleteType::class, [
                'disabled' => $options['disabled_part'],
                'widget' => false,
            ])
            ->add('orderUpToQuantity', QuantityType::class, [
                'label' => 'Заказывать до',
            ])
            ->add('orderFromQuantity', QuantityType::class, [
                'label' => 'Когда на складе осталось',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RequiredAvailabilityDto::class,
            'disabled_part' => true,
        ]);
    }
}
