<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Embeddable\CarEngine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarEngineType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Модель ДВС',
            ])
            ->add('type', EngineTypeType::class, [
                'label' => 'Тип двигателя',
            ])
            ->add('capacity', EngineCapacityType::class, [
                'label' => 'Объём двигателя',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => CarEngine::class,
            ]);
    }
}
