<?php

namespace App\Vehicle\Infrastructure\Form;

use App\Vehicle\Enum\AirIntake;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AirIntakeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => AirIntake::all(),
            'choice_label' => fn (AirIntake $enum) => $enum->toName(),
            'choice_value' => fn (?AirIntake $enum) => null === $enum ? null : $enum->toId(),
            'placeholder' => 'Не определено',
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
