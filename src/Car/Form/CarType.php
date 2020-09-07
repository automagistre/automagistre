<?php

declare(strict_types=1);

namespace App\Car\Form;

use App\Car\Form\DTO\CarCreate;
use App\Shared\Form\EmptyStringAndCaseTransformer;
use App\Vehicle\Form\BodyTypeType;
use App\Vehicle\Form\EquipmentType;
use App\Vehicle\Form\VehicleAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vehicleId', VehicleAutocompleteType::class)
            ->add('year', IntegerType::class, [
                'label' => 'Год выпуска',
                'required' => false,
            ])
            ->add('caseType', BodyTypeType::class, [
                'label' => 'Тип кузова',
                'required' => false,
            ])
            ->add('identifier', TextType::class, [
                'label' => 'Идентификатор',
                'required' => false,
                'help' => 'VIN, № Кузова/Шасси...',
            ])
            ->add('gosnomer', null, [
                'label' => 'Гос. Номер',
                'required' => false,
            ])
            ->add('description', TextType::class, [
                'label' => 'Описание',
                'required' => false,
            ])
            ->add('equipment', EquipmentType::class, [
                'label' => false,
                'required' => false,
            ]);

        $builder->get('identifier')->addViewTransformer(new EmptyStringAndCaseTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarCreate::class,
        ]);
    }
}
