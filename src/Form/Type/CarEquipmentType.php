<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Embeddable\CarEquipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarEquipmentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('engine', CarEngineType::class, [
                'label' => 'Двигатель',
            ])
            ->add('transmission', CarTransmissionType::class, [
                'label' => 'КПП',
            ])
            ->add('wheelDrive', CarWheelDriveType::class, [
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
                'data_class' => CarEquipment::class,
            ]);
    }
}
