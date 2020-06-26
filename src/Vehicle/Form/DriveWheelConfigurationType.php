<?php

declare(strict_types=1);

namespace App\Vehicle\Form;

use App\Vehicle\Enum\DriveWheelConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class DriveWheelConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => DriveWheelConfiguration::all(),
            'choice_label' => fn (DriveWheelConfiguration $value) => $value->toName(),
            'choice_value' => fn (?DriveWheelConfiguration $value) => null === $value ? null : $value->toId(),
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
