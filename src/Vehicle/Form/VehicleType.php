<?php

declare(strict_types=1);

namespace App\Vehicle\Form;

use App\Manufacturer\Form\ManufacturerAutocompleteType;
use App\Shared\Form\EmptyStringAndCaseTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class VehicleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('manufacturerId', ManufacturerAutocompleteType::class, [
                'required' => true,
            ])
            ->add('name', null, [
                'label' => 'Название',
                'required' => true,
            ])
            ->add('localizedName', null, [
                'label' => 'Название на русском',
                'required' => false,
            ])
            ->add('caseName', null, [
                'label' => 'Кузов',
                'required' => false,
            ])
            ->add('yearFrom', IntegerType::class, [
                'label' => 'Начало производства',
                'required' => false,
            ])
            ->add('yearTill', IntegerType::class, [
                'label' => 'Конец производства',
                'required' => false,
            ])
        ;

        $builder->get('caseName')->addViewTransformer(new EmptyStringAndCaseTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModelCreate::class,
        ]);
    }
}
