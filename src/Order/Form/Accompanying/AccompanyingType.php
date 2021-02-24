<?php

declare(strict_types=1);

namespace App\Order\Form\Accompanying;

use App\Form\Type\MoneyType;
use App\Form\Type\QuantityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AccompanyingType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enabled', CheckboxType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('quantity', QuantityType::class)
            ->add('price', MoneyType::class)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AccompanyingDto::class,
            'label' => false,
        ]);
    }
}
