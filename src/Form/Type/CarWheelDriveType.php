<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Enum\CarWheelDrive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class CarWheelDriveType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => CarWheelDrive::all(),
            'choice_label' => fn (CarWheelDrive $carWheelDrive) => $carWheelDrive->toName(),
            'choice_value' => fn (CarWheelDrive $carWheelDrive) => $carWheelDrive->toId(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
