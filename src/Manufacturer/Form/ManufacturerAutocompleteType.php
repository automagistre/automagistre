<?php

declare(strict_types=1);

namespace App\Manufacturer\Form;

use App\EasyAdmin\Form\AutocompleteType;
use App\Manufacturer\Entity\Manufacturer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ManufacturerAutocompleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Manufacturer::class,
            'label' => 'Производитель',
            'error_bubbling' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'manufacturer_autocomplete';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return AutocompleteType::class;
    }
}
