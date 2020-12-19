<?php

declare(strict_types=1);

namespace App\Vehicle\Form;

use App\Vehicle\Enum\FuelType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class FuelTypeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => FuelType::all(),
            'choice_label' => fn (FuelType $fuelType) => $fuelType->toName(),
            'choice_value' => fn (?FuelType $fuelType) => null === $fuelType ? null : $fuelType->toId(),
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
