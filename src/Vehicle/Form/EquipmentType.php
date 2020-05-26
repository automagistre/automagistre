<?php

declare(strict_types=1);

namespace App\Vehicle\Form;

use App\Vehicle\Entity\Embedded\Equipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EquipmentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('engine', EngineType::class, [
                'label' => 'Двигатель',
            ])
            ->add('transmission', TransmissionType::class, [
                'label' => 'КПП',
            ])
            ->add('wheelDrive', DriveWheelConfigurationType::class, [
                'label' => 'Привод',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Equipment::class,
            ]);
    }
}
