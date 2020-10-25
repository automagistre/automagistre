<?php

declare(strict_types=1);

namespace App\Order\Form\Feedback;

use App\Order\Enum\OrderSatisfaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SatisfactionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => OrderSatisfaction::all(),
            'choice_label' => fn (?OrderSatisfaction $enum) => null !== $enum ? $enum->toDisplayName() : null,
            'choice_value' => fn (?OrderSatisfaction $enum) => null !== $enum ? $enum->toId() : null,
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
