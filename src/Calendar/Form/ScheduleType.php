<?php

declare(strict_types=1);

namespace App\Calendar\Form;

use function array_combine;
use DateTimeImmutable;
use function range;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ScheduleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $now = new DateTimeImmutable();

        $builder
            ->add('date', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'hours' => array_combine(range(10, 23), range(10, 23)),
                'minutes' => [0 => 0, 30 => 30],
                'label' => 'Время записи',
            ])
            ->add('duration', DateIntervalType::class, [
                'with_days' => false,
                'with_hours' => true,
                'with_invert' => false,
                'with_minutes' => true,
                'with_months' => false,
                'with_seconds' => false,
                'with_weeks' => false,
                'with_years' => false,
                'minutes' => [0 => 0, 30 => 30],
                'label' => false,
                'labels' => [
                    'hours' => 'Часы',
                    'minutes' => 'Минуты',
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScheduleDto::class,
            'label' => false,
        ]);
    }
}
