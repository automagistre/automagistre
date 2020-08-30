<?php

declare(strict_types=1);

namespace App\Vehicle\Form;

use App\EasyAdmin\Form\AutocompleteType;
use App\Vehicle\Entity\Model;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class VehicleAutocompleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Model::class,
            'label' => 'Модель',
            'help' => 'Проивзодитель, Модель, Год, Поколение, Комплектация, Лошадинные силы',
            'error_bubbling' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'vehicle_autocomplete';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return AutocompleteType::class;
    }
}
