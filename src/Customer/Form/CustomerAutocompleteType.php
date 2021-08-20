<?php

declare(strict_types=1);

namespace App\Customer\Form;

use App\Customer\Entity\CustomerView;
use App\EasyAdmin\Form\AutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerAutocompleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => CustomerView::class,
            'label' => 'Заказчик',
            'error_bubbling' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'customer_autocomplete';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return AutocompleteType::class;
    }
}
