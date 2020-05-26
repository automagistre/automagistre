<?php

declare(strict_types=1);

namespace App\Vehicle\Form;

use App\Vehicle\Entity\Embedded\Engine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class EngineType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Модель',
            ])
            ->add('type', FuelTypeType::class, [
                'label' => 'Тип',
            ])
            ->add('airIntake', AirIntakeType::class, [
                'label' => 'Наддув',
            ])
            ->add('injection', InjectionType::class, [
                'label' => 'Впрыск',
            ])
            ->add('capacity', EngineCapacityType::class, [
                'label' => 'Объём',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Engine::class,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'car_engine';
    }
}
