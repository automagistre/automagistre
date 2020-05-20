<?php

namespace App\Vehicle\Infrastructure\Form;

use App\Vehicle\Enum\Injection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class InjectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => Injection::all(),
            'choice_label' => fn (Injection $enum) => $enum->toName(),
            'choice_value' => fn (?Injection $enum) => null === $enum ? null : $enum->toId(),
            'placeholder' => null,
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
