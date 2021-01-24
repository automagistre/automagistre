<?php

declare(strict_types=1);

namespace App\Part\Entity;

use App\Part\Enum\Unit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UnitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'Единица изменерия',
            'choices' => Unit::all(),
            'choice_label' => fn (Unit $unit) => $unit->toLabel(),
            'choice_value' => fn (?Unit $unit) => null === $unit ? null : $unit->toId(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
