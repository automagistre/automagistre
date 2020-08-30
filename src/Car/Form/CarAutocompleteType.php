<?php

declare(strict_types=1);

namespace App\Car\Form;

use App\Car\Entity\Car;
use App\EasyAdmin\Form\AutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CarAutocompleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Car::class,
            'label' => 'Автомобиль',
            'error_bubbling' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'car_autocomplete';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return AutocompleteType::class;
    }
}
