<?php

declare(strict_types=1);

namespace App\Part\Form;

use App\Form\Type\MoneyType;
use App\Manufacturer\Form\ManufacturerAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PartType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('manufacturerId', ManufacturerAutocompleteType::class)
            ->add('number', PartNumberType::class)
            ->add('name', null, [
                'label' => 'Название',
            ])
            ->add('price', MoneyType::class, [
                'required' => false,
            ])
            ->add('discount', MoneyType::class, [
                'label' => 'Скидка',
                'required' => false,
            ])
            ->add('universal', CheckboxType::class, [
                'label' => 'Универсальная',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PartDto::class,
        ]);
    }
}
