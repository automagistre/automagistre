<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\EasyAdmin\Form\AutocompleteType;
use App\Part\Entity\Part;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PartAutocompleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Part::class,
            'label' => 'Запчасть',
            'error_bubbling' => false,
            'widget' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'part_autocomplete';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return AutocompleteType::class;
    }
}
